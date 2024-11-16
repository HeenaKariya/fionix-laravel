<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    // admin dashboard
    public function dashboard(Request $request)
    {
        // return view('admin.dashboard');
        
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $users = User::all();  
        // return view('admin.dashboard', compact('users'));
        return view('admin.dashboard', [
            'users' => $users,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);  
    }
    public function userList()
    {
        $users = User::all();  
        return view('admin.userlist', compact('users'));  
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role', function($row){
                    return $row->roles->pluck('name')->join(', ');
                })
                ->addColumn('action', function($row){
                    $editUrl = route('admin.users.edit', $row->id);
                    $deleteUrl = route('admin.users.delete', $row->id);
                    return '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Edit</a>
                            <a href="'.$deleteUrl.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function createUser()
    {
        $roles = Role::all();
        return view('admin.create_user', compact('roles'));
    }
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:2|confirmed',
            'mobile_no' => 'required|string|max:10|unique:users',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_no' => $request->mobile_no,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully.');
    }
    public function showChangePasswordForm($id)
    {
        $user = User::findOrFail($id);
        return view('admin.change_password', compact('user'));
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:3|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Invalidate all sessions for the user
        DB::table('sessions')->where('user_id', $user->id)->delete();
        
        

        return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully.');
    }
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.edit_user', compact('user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'mobile_no' => 'required|string|max:15|unique:users,mobile_no,'.$id,
            'password' => 'nullable|string|min:3|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles($request->role);

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully.');
    }
}
