<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\ActivityLog;

class UserController extends Controller
{
    // 1️⃣ List Users
    public function index()
    {
        $currentUser = auth()->user();

        if ($currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            $users = User::with('roles')->get();
        } else {
            abort(403);
        }

        return view('admin.users.index', compact('users'));
    }

    // 2️⃣ Show Create Form
    public function create()
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            abort(403);
        }

        // Ensure basic roles exist in this tenant
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'User']);

        // Fetch roles to display in the dropdown
        $roles = Role::whereIn('name', ['Admin', 'User'])->get();

        return view('admin.users.create', compact('roles'));
    }

    // 3️⃣ Store User
    public function store(Request $request)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        // 🔥 USER LIMIT CHECK (MAX 5 PER SHOP)
        $userCount = User::count();

        if ($userCount >= 5) {
            return redirect()->back()->with('error', 'User limit reached! Max 5 users allowed per shop.');
        }

        // ✅ Admin restrictions
        if ($currentUser->hasAnyRole(['Admin', 'admin'])) {
            if (!in_array(strtolower($request->role), ['admin', 'user'])) {
                return redirect()->back()->with('error', 'Invalid role selected.');
            }
        }

        // 🔥 CREATE USER
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Role
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    // 4️⃣ Show Edit Form
    public function edit(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            $roles = Role::whereIn('name', ['Admin', 'User'])->get();
        } else {
            abort(403);
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    // 5️⃣ Update User
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            $user->syncRoles([$request->role]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Updated',
            'description' => 'User "' . $user->name . '" was updated'
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    // 6️⃣ Delete User
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'Admin', 'admin'])) {
            abort(403);
        }
        if ($user->id == $currentUser->id) {
            abort(403);
        }

        $deletedUserName = $user->name;

        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Deleted',
            'description' => 'User "' . $deletedUserName . '" was deleted'
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}