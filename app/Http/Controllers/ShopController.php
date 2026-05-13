<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return view('superadmin.shops.index', compact('shops'));
    }

    public function create()
    {
        return view('superadmin.create_shop');
    }

    public function store(Request $request)
    {
        // Redirect to SuperAdmin storeShop
        return app(\App\Http\Controllers\SuperAdmin\DashboardController::class)->storeShop($request);
    }

    public function show($id)
    {
        $shop = Shop::findOrFail($id);
        return view('superadmin.show_shop', compact('shop'));
    }

    public function edit($id)
    {
        return app(\App\Http\Controllers\SuperAdmin\DashboardController::class)->editShop($id);
    }

    public function update(Request $request, $id)
    {
        return app(\App\Http\Controllers\SuperAdmin\DashboardController::class)->updateShop($request, $id);
    }

    public function destroy($id)
    {
        return app(\App\Http\Controllers\SuperAdmin\DashboardController::class)->deleteShop($id);
    }
}
