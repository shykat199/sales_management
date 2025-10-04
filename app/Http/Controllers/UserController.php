<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequet;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = User::query();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $updateUrl = route('user.update-user',$row->id); // fill if needed
                    $deleteUrl = route('user.delete-user', $row->id);

                    $actions = '<div class="d-flex align-items-center gap-2">';

                    $actions .= '<a data-role="' . $row->role . '"
                                    data-address="' . htmlspecialchars($row->address, ENT_QUOTES) . '"
                                    data-url="' . $updateUrl . '"
                                    data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '"
                                    data-email="' . htmlspecialchars($row->email, ENT_QUOTES) . '"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBlogModal">
                                    <i class="fa-regular fa-pen-to-square fa-2x text-warning" aria-hidden="true"></i>
                                </a>';


                    $actions .= '<a href="javascript:void(0);" onclick="showSwal(\'passing-parameter-execute-cancel\', \'' . e($deleteUrl) . '\')">';
                    $actions .= '<i class="fa-solid fa-trash fa-2x text-danger" aria-hidden="true"></i>';
                    $actions .= '</a>';

                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('user.index');
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

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        $user->delete();
        toast('User deleted successfully!','success');
        return redirect()->back();
    }
}
