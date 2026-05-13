@extends('layouts.admin_master')

@section('content')

<style>
/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
    .flex-box {
        flex-direction: column !important;
    }

    .stat-box {
        flex: 0 0 100% !important;
        margin: 10px 0 !important;
    }

    .top-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px;
    }

    .action-buttons form {
        display: block !important;
        margin-bottom: 10px;
    }
}
</style>

<div style="background: linear-gradient(to right, #dbeafe, #eff6ff); min-height:100vh; padding:30px;">

    <h1 style="color:#dc2626; font-weight:bold; text-align:center; margin-bottom:40px;">
        SUPER ADMIN DASHBOARD
    </h1>

<div style="text-align:right; margin-bottom:20px;">
    <a href="{{ route('superadmin.createShop') }}"
       style="background:#16a34a; color:white; padding:10px 20px; border-radius:8px; text-decoration:none;">
        ➕ Add New Shop
    </a>
</div>

{{-- Expired Shops --}}
@if(isset($expiredShops) && $expiredShops->count() > 0)
<div style="background:#fee2e2; border-left:6px solid #dc2626; padding:20px; margin-bottom:30px; border-radius:10px;">
    <h3 style="color:#991b1b; margin-bottom:15px;">
        ⚠ Expired Shops ({{ $expiredShops->count() }})
    </h3>

    <ul style="margin:0; padding-left:20px; color:#7f1d1d;">
        @foreach($expiredShops as $shop)
            <li>
                <strong>{{ $shop->name }}</strong>
                - Expired on 
                {{ \Carbon\Carbon::parse($shop->subscription_ends_at)->format('d M Y') }}

                <form action="{{ route('superadmin.renewShop', $shop->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" style="background:#2563eb; color:white; padding:5px 10px; border-radius:5px; margin-left:10px;">
                        Renew
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endif

{{-- Shops --}}
@foreach($shops as $shop)

<div style="background:white; border-radius:15px; padding:20px; box-shadow:0 10px 25px rgba(0,0,0,0.1); margin-bottom:30px;">

    <h3 style="background:#2563eb; color:white; padding:10px; border-radius:8px;">
        {{ $shop->name }}
    </h3>

    <div class="flex-box" style="display:flex; flex-wrap:wrap; margin-top:15px;">

        @php
            $items = [
                'Total Products' => 'total_products',
                'Total Orders' => 'total_orders',
                'Total Customers' => 'total_customers',
                'Total Employees' => 'total_employees',
                'Total Suppliers' => 'total_suppliers',
                'Total Expenses' => 'total_expenses',
                'Total Salaries' => 'total_salaries',
                'Total Invoices' => 'total_invoices',
                'Total Payments' => 'total_payments',
            ];
        @endphp

        @foreach($items as $label => $key)
        <div class="stat-box" style="flex:0 0 30%; margin:10px; background:#f1f5f9; border-radius:10px; padding:15px; text-align:center;">
            <h5 style="color:#1e3a8a;">{{ $label }}</h5>
            <h3 style="color:#dc2626;">
                {{ $stats[$shop->id][$key] ?? 0 }}
            </h3>
        </div>
        @endforeach

    </div>
</div>

@endforeach

{{-- STATUS + BUTTONS --}}
@foreach($shops as $shop)

<div style="background:white; border-radius:15px; padding:20px; box-shadow:0 10px 25px rgba(0,0,0,0.1); margin-bottom:30px;">

    <div class="top-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="color:#1e3a8a;">{{ $shop->name }}</h3>

        @if($shop->is_active)
            <span style="background:#dcfce7; color:#166534; padding:6px 15px; border-radius:20px;">✅ Active</span>
        @else
            <span style="background:#fee2e2; color:#991b1b; padding:6px 15px; border-radius:20px;">❌ Closed</span>
        @endif
    </div>

    <div style="margin-top:10px; color:#475569; font-size:0.9em;">
        <strong>Admin Email:</strong> {{ $shop->admin_email }} | 
        <strong>Admin Password:</strong> {{ $shop->admin_password }}
    </div>

    <hr style="margin:15px 0;">

    <div class="action-buttons" style="margin-top:10px;">

        @if($shop->is_active)
        <form action="{{ route('superadmin.closeShop', $shop->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" style="background:#ef4444; color:white; padding:8px 18px; border:none; border-radius:8px;">
                Close Shop
            </button>
        </form>

        <a href="{{ route('superadmin.impersonate', $shop->id) }}" target="_blank" style="background:#10b981; color:white; padding:8px 18px; border:none; border-radius:8px; text-decoration:none; margin-left:10px; display:inline-block;">
            🚀 Login to Shop
        </a>
        @else

        <form action="{{ route('superadmin.renewShop', $shop->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" style="background:#2563eb; color:white; padding:8px 18px; border:none; border-radius:8px;">
                Renew Subscription
            </button>
        </form>

        <form action="{{ route('superadmin.deleteShop', $shop->id) }}" method="POST" style="display:inline-block; margin-left:10px;">
            @csrf
            <button type="submit" style="background:#000; color:white; padding:8px 18px; border:none; border-radius:8px;">
                Delete Shop
            </button>
        </form>

        <form action="{{ route('superadmin.editShop', $shop->id) }}" method="GET" style="display:inline-block; margin-left:10px;">
            <button type="submit" style="background:#f59e0b; color:white; padding:8px 18px; border:none; border-radius:8px;">
                Edit Shop
            </button>
        </form>

        <a href="{{ route('superadmin.impersonate', $shop->id) }}" target="_blank" style="background:#10b981; color:white; padding:8px 18px; border:none; border-radius:8px; text-decoration:none; margin-left:10px; display:inline-block;">
            🚀 Login to Shop
        </a>

        @endif

    </div>

</div>

@endforeach

</div>

@endsection