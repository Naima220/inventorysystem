<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return view('superadmin.subscriptions.index', compact('shops'));
    }

    public function renew($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->subscription_ends_at = now()->addDays(30);
        $shop->is_active = true;
        $shop->save();

        return back()->with('success','Subscription renewed for 30 days.');
    }

    public function deactivate($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->is_active = false;
        $shop->save();

        return back()->with('success','Shop deactivated.');
    }
}