<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function saveUser(UserFormRequet $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'password' => \Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        toast('User created successfully!','success');
        return redirect()->back();
    }

    public function updateUser(Request $request, $id)
    {

        try {

            $request->validate([
                'name' => 'required|string|min:3|max:100',
                'email' => [
                    'required',
                    'email',
                ],
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:1,2',
                'address' => 'nullable|string|max:255',
            ]);

            $user = User::find($id);

            if (!$user){
                abort(404);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => isset($request->password) ? \Hash::make($request->password) : $user->password,
                'role' => $request->role,
                'address' => $request->address,
            ]);

            toast('User updated successfully!', 'success');

            return redirect()->back();

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
