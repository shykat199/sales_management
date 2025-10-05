<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\UserFormRequet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
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

    public function createProduct()
    {
        $data['product']=null;
        $data['title']='Add Product';
        return view('product.action',$data);
    }

    public function saveProduct(ProductRequest $request)
    {
        $validated = $request->validated();

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name.'-'.time());
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;
        $product->description = $request->description;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $file->storeAs('products', $fileNameToStore, 'public');
            $product->image = 'products/' . $fileNameToStore;
        }

        $product->save();

        toast('Product created successfully!','success');
        return redirect()->route('product.product-list');
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
