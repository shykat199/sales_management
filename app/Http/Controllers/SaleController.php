<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $type = $request->get('type');
            $data = Product::query();

            switch ($type) {
                case 'active':
                    $data->where('status', ACTIVE_STATUS);
                    break;
                case 'inactive':
                    $data->where('status', INACTIVE_STATUS);
                    break;
                case 'out-of-stock':
                    $data->where('quantity', 0);
                    break;
                case 'trash':
                    $data->onlyTrashed();
                    break;
                default:
                    $data->withTrashed();
                    break;
            }

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('product.product-details', $row->slug);
                    $deleteUrl = route('product.delete-product', $row->id);
                    $actions = '<div class="d-flex align-items-center gap-2">';

                    $actions .= '<a href="' . $editUrl . '">
                                    <i class="fa-regular fa-pen-to-square fa-2x text-warning" aria-hidden="true"></i>
                                 </a>';

                    if (!$row->trashed()) {
                        $actions .= '<a href="javascript:void(0);" onclick="showSwal(\'passing-parameter-execute-cancel\', \'' . e($deleteUrl) . '\')">
                                        <i class="fa-solid fa-trash fa-2x text-danger" aria-hidden="true"></i>
                                     </a>';
                    }

                    if ($row->trashed()) {
                        $restoreUrl = route('product.product-restore', $row->id);
                        $actions .= '<a href="javascript:void(0);" onclick="showSwal(\'passing-parameter-execute-restore\', \'' . e($restoreUrl) . '\')">
                                        <i class="fa-solid fa-recycle fa-2x text-success" aria-hidden="true"></i>
                                     </a>';
                    }

                    $actions .= '</div>';

                    return $actions;
                })
                ->addColumn('image', function ($row) {
                    $imageUrl = !empty($row->image) ? asset('storage/' . $row->image) : asset('default/no-product.png');
                    return '<img src="'.$imageUrl.'" alt="Product Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;">';
                })
                ->addColumn('stock_status', function ($row) {
                    return $row->quantity > 0
                        ? '<span class="badge border border-success text-success">In Stock</span>'
                        : '<span class="badge border border-danger text-danger">Out Of Stock</span>';
                })
                ->rawColumns(['action','image','stock_status'])
                ->make(true);
        }

        $counts = Product::selectRaw("
        COUNT(*) as total,
        COUNT(CASE WHEN status = 1 THEN 1 END) as active,
        COUNT(CASE WHEN status = 2 THEN 1 END) as inactive,
        COUNT(CASE WHEN quantity = 0 THEN 1 END) as out_of_stock
        ")
            ->first()
            ->toArray();

        $counts['trashed'] = Product::onlyTrashed()->count();

        return view('product.index',[
            'counts' => $counts,
        ]);
    }

    public function createSale()
    {
        $data['sale']=null;
        $data['title']='Add Sale';
        return view('sale.action',$data);
    }

    public function searchCustomer(Request $request)
    {
        $query = $request->get('q');

        $customers = User::where('name', 'like', "%$query%")
            ->where('role',USER_ROLE)->limit(20)->get(['id', 'name']);

        return response()->json($customers);
    }

    public function searchProduct(Request $request)
    {
        $query = $request->get('q');

        $products = Product::where('name', 'like', "%$query%")
            ->limit(20)
            ->get(['id', 'name', 'price','quantity']);

        return response()->json(
            $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                ];
            })
        );
    }


    public function saveSale(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'products'    => 'required|array|min:1',
            'comment'    => 'nullable',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.qty'      => 'required|numeric|min:1',
            'products.*.price'    => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.total'    => 'required|numeric|min:0',
            'grandTotal'          => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {

            $totals = calculateSaleTotal($validated['products']);

            $sale = Sale::create([
                'user_id'        => auth()->id(),
                'customer_id'    => $validated['customer_id'],
                'sale_date'      => now(),
                'subtotal'       => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'total_amount'   => $validated['grandTotal'],
            ]);

            // Save sale items
            foreach ($validated['products'] as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['qty'],
                    'price'      => $item['price'],
                    'discount'   => $item['discount'] ?? 0,
                    'total'      => $item['total'],
                ]);

                \App\Models\Product::where('id', $item['id'])->decrement('quantity', $item['qty']);

            }


            if ($request->filled('comment')) {
                $sale->note()->create([
                    'noteable_id'   => $sale->id,
                    'noteable_type' => Sale::class,
                    'note'          => $request->comment
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sale saved successfully',
                'sale_id' => $sale->id,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function editProduct($slug)
    {
        $product = Product::withTrashed()->where('slug', $slug)->first();
        if (!$product) {
            abort(404);
        }
        $data['product']=$product;
        $data['title']='Update Product';
        return view('product.action',$data);
    }

    public function updateProduct(Request $request, $slug)
    {

        try {

            $product = Product::where('slug', $slug)->firstOrFail();

            $request->validate([
                'name' => 'required|string|min:3',
                'sku' => 'required|string|min:2',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'status' => 'required|in:0,1',
                'description' => 'required|string|min:5',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->status = $request->status;
            $product->description = $request->description;


            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $file = $request->file('image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $file->storeAs('products', $fileNameToStore, 'public');
                $product->image = 'products/' . $fileNameToStore;
            }

            $product->save();


            toast('Product updated successfully!', 'success');

            return redirect()->route('product.product-details', $product->slug);

        }catch (\Exception $exception){
            dd($exception->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            abort(404);
        }
        $product->delete();
        toast('Product deleted successfully!','success');
        return redirect()->back();
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        toast('Product restored successfully.!','success');
        return redirect()->back();
    }
}
