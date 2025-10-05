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

            $data = Sale::with(['customer','createdBy'])->withTrashed();

            if ($request->filled('customer')) {
                $data->where('customer_id', $request->customer);
            }

            if ($request->filled('product')) {
                $data->whereHas('items', function($q) use ($request) {
                    $q->where('product_id', $request->product);
                });
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $data->whereBetween('sale_date', [$request->start_date, $request->end_date]);
            }

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('sale.sale-details', $row->id);
                    $deleteUrl = route('sale.delete-sale', $row->id);
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
                        $restoreUrl = route('sale.sale-restore', $row->id);
                        $actions .= '<a href="javascript:void(0);" onclick="showSwal(\'passing-parameter-execute-restore\', \'' . e($restoreUrl) . '\')">
                                        <i class="fa-solid fa-recycle fa-2x text-success" aria-hidden="true"></i>
                                     </a>';
                    }

                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('sale.index');
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

    public function editSale($id)
    {
        $sales = Sale::with(['customer','items.product','note'])->withTrashed()->where('id', $id)->first();
        if (!$sales) {
            abort(404);
        }
        $data['sale']=$sales;
        $data['title']='Update Sale record';
        return view('sale.edit-action',$data);
    }

    public function updateSale(Request $request)
    {
        $validated = $request->validate([
            'saleId'            => 'required|exists:sales,id',
            'customer_id'       => 'required|exists:users,id',
            'products'          => 'required|array|min:1',
            'comment'           => 'nullable|string',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.qty'      => 'required|numeric|min:1',
            'products.*.price'    => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.total'    => 'required|numeric|min:0',
            'grandTotal'          => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($validated['saleId']);

            $sale->items()->delete();

            // Recalculate totals
            $totals = calculateSaleTotal($validated['products']);

            // Update sale
            $sale->update([
                'customer_id'    => $validated['customer_id'],
                'subtotal'       => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'total_amount'   => $validated['grandTotal'],
            ]);

            foreach ($validated['products'] as $item) {
                if (!empty($item['salesItemId'])) {
                    $salesItem = SaleItem::findOrFail($item['salesItemId']);

                    $diff = $item['qty'] - $salesItem->quantity;
                    if ($diff > 0) {
                        Product::where('id', $item['id'])->decrement('quantity', $diff);
                    } elseif ($diff < 0) {
                        Product::where('id', $item['id'])->increment('quantity', abs($diff));
                    }

                    $salesItem->update([
                        'product_id' => $item['id'],
                        'quantity'   => $item['qty'],
                        'price'      => $item['price'],
                        'discount'   => $item['discount'] ?? 0,
                        'total'      => $item['total'],
                    ]);
                } else {

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $item['id'],
                        'quantity'   => $item['qty'],
                        'price'      => $item['price'],
                        'discount'   => $item['discount'] ?? 0,
                        'total'      => $item['total'],
                    ]);
                    Product::where('id', $item['id'])->decrement('quantity', $item['qty']);
                }
            }

            // Notes
            if ($request->filled('comment')) {
                if ($sale->note()->exists()) {
                    // Update the most recent note
                    $sale->note()->latest()->first()->update([
                        'note' => $request->comment,
                    ]);
                } else {
                    // Create a new note if none exist
                    $sale->note()->create([
                        'note' => $request->comment,
                    ]);
                }
            }


            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sale updated successfully',
                'sale_id' => $sale->id,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Update failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function deleteSale($id)
    {
        $sale = Sale::find($id);
        if (!$sale) {
            abort(404);
        }
        SaleItem::where('sale_id', $id)->delete();
        $sale->delete();
        toast('Sale record deleted successfully!','success');
        return redirect()->back();
    }

    public function restore($id)
    {
        $sale = Sale::onlyTrashed()->findOrFail($id);

        $sale->items()->withTrashed()->restore();

        $sale->restore();

        toast('Sale record restored successfully!', 'success');
        return redirect()->back();
    }

}
