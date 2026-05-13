<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function reply(Request $request)
    {
        $msg = strtolower(trim($request->message));

        // PRODUCT QUERIES
        if (str_contains($msg, 'product cusub') || str_contains($msg, 'new product')) {
            $count = DB::table('products')->whereDate('created_at', Carbon::today())->count();
            return response()->json(['reply' => "Maanta waxaa la geliyay $count product cusub."]);
        }

        if (str_contains($msg, 'stock out')) {
            $count = DB::table('products')->where('stock', 0)->count();
            return response()->json(['reply' => "$count products ayaa stock out ah."]);
        }

        if (str_contains($msg, 'low stock')) {
            $count = DB::table('products')
                ->whereColumn('stock', '<=', 'low_stock_limit')
                ->where('stock', '>', 0)
                ->count();

            return response()->json(['reply' => "$count products ayaa low stock ah."]);
        }

        if (str_contains($msg, 'available')) {
            $count = DB::table('products')->where('stock', '>', 0)->count();
            return response()->json(['reply' => "$count products ayaa available ah."]);
        }

        // ORDER QUERIES
        if (str_contains($msg, 'pending order')) {
            $count = DB::table('orders')->where('order_status', 0)->count();
            return response()->json(['reply' => "$count orders ayaa pending ah."]);
        }

        if (str_contains($msg, 'delivered order') || str_contains($msg, 'delivery order')) {
            $count = DB::table('orders')->where('order_status', 1)->count();
            return response()->json(['reply' => "$count orders ayaa delivered ah."]);
        }

        if (str_contains($msg, 'orders maanta')) {
            $count = DB::table('orders')->whereDate('created_at', Carbon::today())->count();
            return response()->json(['reply' => "Maanta waxaa dhacay $count orders."]);
        }

        // SALES QUERIES
        if (
            str_contains($msg, 'sales maanta') ||
            str_contains($msg, 'inta iib maanta dhacay')
        ) {
            $count = DB::table('invoices')->whereDate('created_at', Carbon::today())->count();
            return response()->json(['reply' => "Maanta waxaa dhacay $count sales."]);
        }

        if (
            str_contains($msg, 'lacagta maanta') ||
            str_contains($msg, 'isku geyn maanta')
        ) {
            $total = DB::table('invoice_items')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_price');

            return response()->json([
                'reply' => "Lacagta guud ee maanta waa $" . number_format($total, 2)
            ]);
        }

        if (
            str_contains($msg, 'items maanta') ||
            str_contains($msg, 'inta shay la iibiyay maanta')
        ) {
            $qty = DB::table('invoice_items')
                ->whereDate('created_at', Carbon::today())
                ->sum('quantity');

            return response()->json([
                'reply' => "Maanta waxaa la iibiyay $qty items."
            ]);
        }

        if (str_contains($msg, 'debt maanta')) {
            $debt = DB::table('invoices')
                ->whereDate('created_at', Carbon::today())
                ->sum('debt');

            return response()->json([
                'reply' => "Debt maanta waa $" . number_format($debt, 2)
            ]);
        }

        // BASIC REPLIES
        $responses = [
            'hi' => 'Hello 😊 Sideen kuu caawin karaa?',
            'hello' => 'Hello 😊 Sideen kuu caawin karaa?',
            'sale' => 'Tag Sales → Add Sale → Save',
            'product' => 'Tag Products → Add Product → Save',
            'report' => 'Tag Reports section.',
            'debt' => 'Tag Debt section.',
            'salary' => 'Tag Salary section.',
            'expense' => 'Tag Expenses section.',
            'customer' => 'Tag Customers section.',
            'supplier' => 'Tag Suppliers section.',
            'stock' => 'Tag Products section.',
            'profit' => 'Profit report-ka waxaad ka heli kartaa Reports.',
            'invoice' => 'Invoice waxaa laga sameeyaa Sales kadib Save.',
            'user' => 'Tag Users section.',
            'order' => 'Tag Orders section.',
        ];

        foreach ($responses as $key => $reply) {
            if (str_contains($msg, $key)) {
                return response()->json(['reply' => $reply]);
            }
        }

        return response()->json([
            'reply' => 'Isku day: sales maanta, lacagta maanta, low stock, pending order 😊'
        ]);
    }
}