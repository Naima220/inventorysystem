<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        $allUsers = [];

        foreach ($shops as $shop) {
            try {
                tenancy()->initialize($shop);
                
                $users = User::with('roles')->get()->map(function($user) use ($shop) {
                    $user->shop_name = $shop->name;
                    $user->shop_id = $shop->id;
                    $user->role_name = $user->roles->pluck('name')->first() ?? 'User';
                    return $user;
                });

                $allUsers = array_merge($allUsers, $users->toArray());
                
                tenancy()->end();
            } catch (\Exception $e) {
                // Skip if shop database is not reachable
            }
        }

        return view('superadmin.users.index', compact('allUsers'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'shop_id' => 'required',
            'user_id' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $shop = Shop::findOrFail($request->shop_id);

        try {
            tenancy()->initialize($shop);
            
            $user = User::findOrFail($request->user_id);
            $user->password = Hash::make($request->new_password);
            $user->save();
            
            tenancy()->end();

            return back()->with('success', "Password updated successfully for user: {$user->email}");
        } catch (\Exception $e) {
            if (tenancy()->initialized) {
                tenancy()->end();
            }
            return back()->with('error', "Error updating password: " . $e->getMessage());
        }
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'shop_id' => 'required',
            'user_id' => 'required',
        ]);

        $shop = Shop::findOrFail($request->shop_id);

        try {
            tenancy()->initialize($shop);
            
            $user = User::findOrFail($request->user_id);
            
            // Do not allow deleting superadmin or the user deleting themselves
            $user->delete();
            
            tenancy()->end();

            return back()->with('success', "User deleted successfully from shop: {$shop->name}");
        } catch (\Exception $e) {
            if (tenancy()->initialized) {
                tenancy()->end();
            }
            return back()->with('error', "Error deleting user: " . $e->getMessage());
        }
    }
}
