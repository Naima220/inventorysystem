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

        // 1. GENERAL SUMMARY / REPORT / MAANTA
        if (
            str_contains($msg, 'warbixin') || 
            str_contains($msg, 'summary') || 
            str_contains($msg, 'today') || 
            str_contains($msg, 'maanta') || 
            str_contains($msg, 'shaqada') ||
            $msg === 'hi' || 
            $msg === 'hello'
        ) {
            $salesCount = DB::table('invoices')->whereDate('created_at', Carbon::today())->count();
            $salesTotal = DB::table('invoice_items')->whereDate('created_at', Carbon::today())->sum('total_price');
            $expensesTotal = DB::table('expenses')->whereDate('expense_date', Carbon::today())->sum('amount');
            $debtTotal = DB::table('invoices')->whereDate('created_at', Carbon::today())->sum('debt');
            $lowStockCount = DB::table('products')->whereColumn('stock', '<=', 'low_stock_limit')->where('stock', '>', 0)->count();
            $outOfStockCount = DB::table('products')->where('stock', 0)->count();
            $pendingOrders = DB::table('orders')->where('order_status', 0)->count();

            $dateStr = Carbon::today()->format('Y-m-d');
            
            $reply = "📊 **Warbixinta Maanta ($dateStr):**\n\n"
                   . "• **Iibka Dhacay**: $salesCount sales (Total: $" . number_format($salesTotal, 2) . ")\n"
                   . "• **Kharashka (Expenses)**: $" . number_format($expensesTotal, 2) . "\n"
                   . "• **Deynta Cusub (Debt)**: $" . number_format($debtTotal, 2) . "\n"
                   . "• **Dalabaadka Sugan (Pending)**: $pendingOrders orders\n"
                   . "• **Alaabta Yaraatay (Low Stock)**: $lowStockCount items\n"
                   . "• **Alaabta Go'an (Out of Stock)**: $outOfStockCount items\n\n"
                   . "😊 Sidee kale oo aan kuu caawin karaa? Isku day inaad wax iga weydiiso deymaha, shaqaalaha, ama alaabta ugu iibka badan!";
            
            return response()->json(['reply' => $reply]);
        }

        // 2. PRODUCT SPECIFIC QUERIES
        // Best selling product
        if (
            str_contains($msg, 'iibka badan') || 
            str_contains($msg, 'best seller') || 
            str_contains($msg, 'iibiyay badan') || 
            str_contains($msg, 'ugu iibka badan') ||
            str_contains($msg, 'ugu iibinta badan')
        ) {
            $bestSeller = DB::table('invoice_items')
                ->join('products', 'invoice_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(invoice_items.quantity) as total_qty'))
                ->groupBy('products.name')
                ->orderBy('total_qty', 'desc')
                ->first();

            if ($bestSeller) {
                return response()->json(['reply' => "🏆 Alaabta ugu iibka badan waa **{$bestSeller->name}**, waxaana la iibiyay **{$bestSeller->total_qty} xabbo** guud ahaan."]);
            }
            return response()->json(['reply' => "Weli wax iib ah kama dhicin nidaamka si loo ogaado alaabta ugu iibka badan."]);
        }

        // Most expensive product
        if (str_contains($msg, 'qaalisan') || str_contains($msg, 'expensive') || str_contains($msg, 'ugu qaalisan')) {
            $expensive = DB::table('products')->orderBy('sales_unit_price', 'desc')->first();
            if ($expensive) {
                return response()->json(['reply' => "💰 Alaabta ugu qaalisan waa **{$expensive->name}** oo qiimaheedu yahay **$" . number_format($expensive->sales_unit_price, 2) . "** (Stock: {$expensive->stock})."]);
            }
            return response()->json(['reply' => "Weli wax alaab ah kuma jiraan database-ka."]);
        }

        // Cheapest product
        if (
            str_contains($msg, 'raqiisan') || 
            str_contains($msg, 'cheapest') || 
            str_contains($msg, 'ugu jaban') || 
            str_contains($msg, 'raqiis') ||
            str_contains($msg, 'ugu raqiisan')
        ) {
            $cheapest = DB::table('products')->where('sales_unit_price', '>', 0)->orderBy('sales_unit_price', 'asc')->first();
            if ($cheapest) {
                return response()->json(['reply' => "🏷️ Alaabta ugu raqiisan waa **{$cheapest->name}** oo qiimaheedu yahay **$" . number_format($cheapest->sales_unit_price, 2) . "** (Stock: {$cheapest->stock})."]);
            }
            return response()->json(['reply' => "Weli wax alaab ah kuma jiraan database-ka."]);
        }

        // Categories / Qeybaha
        if (str_contains($msg, 'categories') || str_contains($msg, 'qeybaha') || str_contains($msg, 'category')) {
            $categories = DB::table('products')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->toArray();

            $count = count($categories);
            if ($count > 0) {
                $list = implode(', ', $categories);
                return response()->json(['reply' => "🗂️ Waxaa ku jira **$count qeybood** oo kala ah: \n_$list_"]);
            }
            return response()->json(['reply' => "Weli wax qeybood ah (categories) looma abuurin alaabta."]);
        }

        // Low stock limit
        if (str_contains($msg, 'low stock') || str_contains($msg, 'alaabta yaraatay') || str_contains($msg, 'yaraaday')) {
            $products = DB::table('products')
                ->whereColumn('stock', '<=', 'low_stock_limit')
                ->where('stock', '>', 0)
                ->get();

            $count = $products->count();
            if ($count > 0) {
                $list = $products->map(fn($p) => "• {$p->name} (Stock: {$p->stock}, Limit: {$p->low_stock_limit})")->implode("\n");
                return response()->json(['reply' => "⚠️ **Low Stock ($count items):**\n$list"]);
            }
            return response()->json(['reply' => "✅ Ma jiraan alaab low stock ah hadda!"]);
        }

        // Stock out / Out of stock
        if (str_contains($msg, 'stock out') || str_contains($msg, 'dhamaaday') || str_contains($msg, 'go\'an')) {
            $products = DB::table('products')->where('stock', 0)->get();
            $count = $products->count();
            if ($count > 0) {
                $list = $products->map(fn($p) => "• {$p->name}")->implode("\n");
                return response()->json(['reply' => "🚫 **Alaabta Dhamaatay ($count items):**\n$list"]);
            }
            return response()->json(['reply' => "✅ Ma jiraan alaab stock out ah hadda!"]);
        }

        // Available
        if (str_contains($msg, 'available') || str_contains($msg, 'alaabta jirta')) {
            $count = DB::table('products')->where('stock', '>', 0)->count();
            return response()->json(['reply' => "📦 Waxaa jira **$count products** oo hadda stock-ka ku jira."]);
        }

        // 3. SALES & PERFORMANCE QUERIES
        // Month sales & revenue
        if (str_contains($msg, 'sales bishaan') || str_contains($msg, 'iibka bishaan') || str_contains($msg, 'lacagta bishaan')) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $salesCount = DB::table('invoices')->where('created_at', '>=', $startOfMonth)->count();
            $salesTotal = DB::table('invoice_items')->where('created_at', '>=', $startOfMonth)->sum('total_price');
            $debtTotal = DB::table('invoices')->where('created_at', '>=', $startOfMonth)->sum('debt');
            
            return response()->json([
                'reply' => "📅 **Iibka Bishaan (" . Carbon::now()->format('F Y') . "):**\n\n"
                         . "• **Iibka Dhacay**: $salesCount sales\n"
                         . "• **Lacagta Soo Gashay**: $" . number_format($salesTotal, 2) . "\n"
                         . "• **Deynta Cusub**: $" . number_format($debtTotal, 2)
            ]);
        }

        // Total sales & revenue ever
        if (
            str_contains($msg, 'sales guud') || 
            str_contains($msg, 'iibka guud') || 
            str_contains($msg, 'lacagta guud') || 
            str_contains($msg, 'iib guud') ||
            str_contains($msg, 'revenue guud')
        ) {
            $salesCount = DB::table('invoices')->count();
            $salesTotal = DB::table('invoice_items')->sum('total_price');
            
            return response()->json([
                'reply' => "📈 **Warbixinta Iibka ee Guud:**\n\n"
                         . "• **Iibka Dhacay Guud ahaan**: $salesCount sales\n"
                         . "• **Lacagta Guud ee Soo Gashay**: $" . number_format($salesTotal, 2)
            ]);
        }

        // Sales today
        if (
            str_contains($msg, 'sales maanta') || 
            str_contains($msg, 'inta iib maanta dhacay') || 
            str_contains($msg, 'iibka maanta') ||
            str_contains($msg, 'iib maanta')
        ) {
            $count = DB::table('invoices')->whereDate('created_at', Carbon::today())->count();
            $total = DB::table('invoice_items')->whereDate('created_at', Carbon::today())->sum('total_price');
            return response()->json([
                'reply' => "🛒 **Iibka Maanta:**\n\n"
                         . "• **Tirada Iibka (Count)**: $count sales\n"
                         . "• **Lacagta Guud (Total Amount)**: $" . number_format($total, 2)
            ]);
        }

        // Money today
        if (str_contains($msg, 'lacagta maanta') || str_contains($msg, 'isku geyn maanta') || str_contains($msg, 'revenue maanta')) {
            $total = DB::table('invoice_items')->whereDate('created_at', Carbon::today())->sum('total_price');
            return response()->json(['reply' => "💵 Lacagta guud ee maanta iibkeeda soo gashay waa **$" . number_format($total, 2) . "**."]);
        }

        // 4. DEBT QUERIES
        // Total debt ever
        if (
            str_contains($msg, 'deymaha guud') || 
            str_contains($msg, 'deynta guud') || 
            str_contains($msg, 'total debt') || 
            str_contains($msg, 'deyn') ||
            str_contains($msg, 'deymaha')
        ) {
            $debtInvoices = DB::table('invoices')->sum('debt');
            $debtTable = DB::table('debts')->sum('amount');
            $totalDebt = max($debtInvoices, $debtTable); // Fallback to either debt calculation

            return response()->json(['reply' => "🔴 **Deynta Guud ee Lagu Leeyahay Macaamiisha** waa **$" . number_format($totalDebt, 2) . "**."]);
        }

        // Highest debt customer
        if (
            str_contains($msg, 'ugu deynta badan') || 
            str_contains($msg, 'deynta badan qofka') || 
            str_contains($msg, 'deyn badan') ||
            str_contains($msg, 'qofka ugu deynta badan')
        ) {
            $highestDebt = DB::table('invoices')
                ->select('customer_name', DB::raw('SUM(debt) as total_debt'))
                ->groupBy('customer_name')
                ->orderBy('total_debt', 'desc')
                ->first();

            if ($highestDebt && $highestDebt->total_debt > 0) {
                return response()->json(['reply' => "👤 Macamiilka ugu deynta badan waa **{$highestDebt->customer_name}** oo lagu leeyahay **$" . number_format($highestDebt->total_debt, 2) . "**."]);
            }
            return response()->json(['reply' => "Ma jiraan macaamiil deyn lagu leeyahay hadda. Waa nadiif!"]);
        }

        // 5. EXPENSE QUERIES
        // Expenses today
        if (str_contains($msg, 'expenses maanta') || str_contains($msg, 'kharashka maanta') || str_contains($msg, 'kharash maanta')) {
            $total = DB::table('expenses')->whereDate('expense_date', Carbon::today())->sum('amount');
            return response()->json(['reply' => "💸 Kharashka (Expenses) ee maanta la bixiyay waa **$" . number_format($total, 2) . "**."]);
        }

        // Expenses this month
        if (str_contains($msg, 'expenses bishaan') || str_contains($msg, 'kharashka bishaan') || str_contains($msg, 'expenses bishaan')) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $total = DB::table('expenses')->where('expense_date', '>=', $startOfMonth)->sum('amount');
            return response()->json(['reply' => "📅 Kharashka (Expenses) ee bishaan la bixiyay waa **$" . number_format($total, 2) . "**."]);
        }

        // Expenses total
        if (str_contains($msg, 'expenses guud') || str_contains($msg, 'kharashka guud') || str_contains($msg, 'kharash guud')) {
            $total = DB::table('expenses')->sum('amount');
            return response()->json(['reply' => "📉 Kharashka guud (Total Expenses) ee la bixiyay waa **$" . number_format($total, 2) . "**."]);
        }

        // 6. EMPLOYEE & SALARY QUERIES
        // Total employees count
        if (str_contains($msg, 'shaqaalaha') || str_contains($msg, 'employee') || str_contains($msg, 'shaqaale')) {
            $employees = DB::table('employees')->get();
            $count = $employees->count();
            if ($count > 0) {
                $list = $employees->map(fn($e) => "• {$e->name} (" . ($e->designation ?? 'Staff') . ")")->implode("\n");
                return response()->json(['reply' => "👥 **Shaqaalaha Dukaanka ($count):**\n$list"]);
            }
            return response()->json(['reply' => "Weli wax shaqaale ah looma diwaangelin dukaankan."]);
        }

        // Total salary payroll
        if (str_contains($msg, 'mushaharka guud') || str_contains($msg, 'salary') || str_contains($msg, 'mushahar')) {
            $totalSalary = DB::table('employees')->sum('salary');
            $paidSalaries = DB::table('salaries')->sum('amount');

            return response()->json([
                'reply' => "💵 **Mushaharka Shaqaalaha:**\n\n"
                         . "• **Mushaharka guud ee bishii** laga rabto dukaanka: $" . number_format($totalSalary, 2) . "\n"
                         . "• **Wixii la bixiyay guud ahaan**: $" . number_format($paidSalaries, 2)
            ]);
        }

        // Order queries (e.g. imisa order, orders today, orders count)
        if (
            str_contains($msg, 'order') || 
            str_contains($msg, 'dalab') || 
            str_contains($msg, 'dalabaad') || 
            str_contains($msg, 'orders')
        ) {
            if (
                str_contains($msg, 'imisa') || 
                str_contains($msg, 'count') || 
                str_contains($msg, 'maanta') || 
                str_contains($msg, 'sugan') || 
                str_contains($msg, 'pending') ||
                str_contains($msg, 'tirada')
            ) {
                $pendingOrders = DB::table('orders')->where('order_status', 0)->count();
                $deliveredOrders = DB::table('orders')->where('order_status', 1)->count();
                $totalOrders = DB::table('orders')->count();
                $todayOrders = DB::table('orders')->whereDate('created_at', Carbon::today())->count();

                return response()->json([
                    'reply' => "📦 **Warbixinta Dalabaadka (Orders):**\n\n"
                             . "• **Wadar ahaan (Total)**: $totalOrders orders\n"
                             . "• **Maanta cusub (Today)**: $todayOrders orders\n"
                             . "• **Sugaya (Pending)**: $pendingOrders orders\n"
                             . "• **La keenay (Delivered)**: $deliveredOrders orders"
                ]);
            }
        }

        // 7. CUSTOMERS & SUPPLIERS
        // Customers
        if (str_contains($msg, 'macaamiisha') || str_contains($msg, 'customer') || str_contains($msg, 'macaamiil')) {
            $count = DB::table('customers')->count();
            $newToday = DB::table('customers')->whereDate('created_at', Carbon::today())->count();
            return response()->json(['reply' => "👤 Macaamiisha guud ee diwaangashan waa **$count**. Maanta waxaa ku soo biiray **$newToday macaamiil cusub**."]);
        }

        // Suppliers
        if (str_contains($msg, 'keenayaasha') || str_contains($msg, 'supplier') || str_contains($msg, 'keenaha')) {
            $count = DB::table('suppliers')->count();
            return response()->json(['reply' => "🚚 Waxaa jira **$count suppliers** (keenayaal alaab) oo diwaangashan."]);
        }

        // 8. HELP & DYNAMIC FALLBACK
        $responses = [
            'sale' => 'Tag Sales → Add Sale → Save',
            'product' => 'Tag Products → Add Product → Save',
            'report' => 'Tag Reports section.',
            'salary' => 'Tag Salary section.',
            'user' => 'Tag Users section.',
            'order' => 'Tag Orders section.',
        ];

        foreach ($responses as $key => $reply) {
            if (str_contains($msg, $key)) {
                return response()->json(['reply' => $reply]);
            }
        }

        // Dynamic helper for unknown queries
        return response()->json([
            'reply' => "Halkan waxaa ku qoran su'aalaha aan ka jawaabi karo:\n\n"
                     . "💡 **Warbixinno**: `warbixin` (summary-ga maanta), `sales bishaan`, `iibka guud`\n"
                     . "📦 **Stock & Alaab**: `stock out` (alaabta dhamaatay), `low stock` (yaraatay), `ugu qaalisan`, `ugu raqiisan`, `ugu iibka badan` (best seller)\n"
                     . "💸 **Kharash & Deymo**: `expenses maanta`, `deymaha guud`, `ugu deynta badan` (qofka)\n"
                     . "👥 **Shaqaale**: `shaqaalaha` (tiradooda & magacyadooda), `mushaharka guud`\n"
                     . "👤 **Macaamiil**: `macaamiisha` (guud ahaan & kuwa maanta)"
        ]);
    }
}