@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">

   <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">

    {{-- Left Side --}}
    <h4 class="mb-0 font-weight-bold">Dashboard</h4>

    {{-- Right Side Notification --}}
    <div class="dropdown">
    <button class="btn btn-light shadow-sm position-relative rounded-circle px-3 py-2"
        data-toggle="dropdown">

        <i class="fas fa-bell" style="color:#0b3c5d;"></i>

        @if($lowStockCount > 0 || $subscriptionMessage)
        <span class="badge badge-danger position-absolute"
            style="top:-5px; right:-5px; font-size:11px;">
            {{ $lowStockCount + ($subscriptionMessage ? 1 : 0) }}
        </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-right p-2 shadow"
        style="width:300px; max-height:320px; overflow:auto; border-radius:12px;">

        <h6 class="dropdown-header font-weight-bold text-primary">
            🔔 Notifications
        </h6>

        {{-- 🔴 Subscription Alert --}}
        @if($subscriptionMessage)
            <div class="dropdown-item">
                <strong>{{ $subscriptionMessage }}</strong>
            </div>
            <div class="dropdown-divider"></div>
        @endif

        {{-- 🟡 Low Stock --}}
        @forelse($lowStockProducts as $product)
            <div class="dropdown-item">
                <strong>{{ $product->name }}</strong><br>
                <small class="text-danger">Stock: {{ $product->stock }}</small>
            </div>
            <div class="dropdown-divider"></div>
        @empty
            <div class="dropdown-item text-success">
                ✅ No low stock products
            </div>
        @endforelse

    </div>
</div>
</div>
    <div class="row dashboard-cards">

        {{-- STOCK --}}
        <div class="col-6 col-md-6 col-lg-3 mb-4">
            <div class="card dash-stat-card h-100 text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="dash-label">Stock</div>
                            <div class="dash-number">{{ $stockCount ?? 0 }}</div>
                        </div>
                        <div class="dash-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('all.product') }}">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
        </div>

        {{-- ORDERS --}}
        <div class="col-6 col-md-6 col-lg-3 mb-4">
            <div class="card dash-stat-card h-100 text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="dash-label">Orders</div>
                            <div class="dash-number">{{ $orderCount ?? 0 }}</div>
                        </div>
                        <div class="dash-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('all.orders') }}">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
        </div>

        {{-- AVAILABLE PRODUCTS --}}
        <div class="col-6 col-md-6 col-lg-3 mb-4">
            <div class="card dash-stat-card h-100 text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="dash-label"> Available Products</div>
                            <div class="dash-number">{{ $availableProducts ?? 0 }}</div>
                        </div>
                        <div class="dash-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('products.available') }}">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
        </div>

        {{-- PENDING ORDERS --}}
        <div class="col-6 col-md-6 col-lg-3 mb-4">
            <div class="card dash-stat-card h-100 text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="dash-label">Pending</div>
                            <div class="dash-number">{{ $pendingOrders ?? 0 }}</div>
                        </div>
                        <div class="dash-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('pending.orders') }}">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
        </div>

    </div>

    {{-- CHARTS --}}
    <div class="row">

        {{-- RESULT CHART --}}
        <div class="col-12 col-lg-8 mb-3">
            <div class="card dashboard-panel h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-chart-bar mr-1"></i> Result</span>
                    <span class="badge badge-warning">Check Now</span>
                </div>
                <div class="card-body">
                    <div style="position:relative;width:100%;height:280px;">
                        <canvas id="myBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- RING CHART --}}
        <div class="col-12 col-lg-4 mb-3">
            <div class="card dashboard-panel h-100">
                <div class="card-body text-center">

                    <div class="dash-ring-wrap mb-3">
                        <canvas id="progressChart" width="220" height="220"></canvas>
                        <div class="dash-ring-center">
                            <div class="dash-ring-number">{{ $orderCount }}</div>
                        </div>
                    </div>

                    <div class="text-left small px-3">

                        <div class="d-flex justify-content-between py-2">
                            <span class="text-primary"><i class="fas fa-circle"></i> Orders</span>
                            <strong>{{ $orderCount }}</strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span class="text-warning"><i class="fas fa-circle"></i> Pending</span>
                            <strong>{{ $pendingOrders }}</strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span class="text-success"><i class="fas fa-circle"></i> Stock</span>
                            <strong>{{ $stockCount }}</strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span class="text-danger"><i class="fas fa-circle"></i> Products</span>
                            <strong>{{ $availableProducts }}</strong>
                        </div>

                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-warning btn-sm mt-3 px-4">
                        Check Now
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- AREA CHART --}}
    <div class="row">
        <div class="col-12 col-lg-8 mb-3">
            <div class="card dashboard-panel">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-chart-area mr-1"></i> Area Chart</span>
                    <span class="badge badge-light">Overview</span>
                </div>
                <div class="card-body">
                    <div style="position:relative;width:100%;height:260px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- QUICK STATS --}}
        <div class="col-12 col-lg-4 mb-3">
            <div class="card dashboard-panel">
                <div class="card-header">
                    <i class="fas fa-th-large mr-1"></i> Quick Stats
                </div>

                <div class="card-body">

                    <div class="mini-stat mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Stock</span>
                            <strong>{{ $stockCount ?? 0 }}</strong>
                        </div>
                        <div class="progress mt-2" style="height:8px;">
                            <div class="progress-bar bg-primary" style="width:70%;"></div>
                        </div>
                    </div>

                    <div class="mini-stat mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Orders</span>
                            <strong>{{ $orderCount ?? 0 }}</strong>
                        </div>
                        <div class="progress mt-2" style="height:8px;">
                            <div class="progress-bar bg-warning" style="width:55%;"></div>
                        </div>
                    </div>

                    <div class="mini-stat mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pending</span>
                            <strong>{{ $pendingOrders ?? 0 }}</strong>
                        </div>
                        <div class="progress mt-2" style="height:8px;">
                            <div class="progress-bar bg-danger" style="width:35%;"></div>
                        </div>
                    </div>

                    <div class="mini-stat">
                        <div class="d-flex justify-content-between">
                            <span>Today Activities</span>
                            <strong>{{ $todayActivities ?? 0 }}</strong>
                        </div>
                        <div class="progress mt-2" style="height:8px;">
                            <div class="progress-bar bg-success" style="width:80%;"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* =========================
   MOBILE CARDS 2 x 2
========================= */
.dashboard-cards .col-6{
    padding-left:8px;
    padding-right:8px;
}

@media (max-width: 767px){
    .dashboard-cards .dash-stat-card{
        min-height: 150px;
    }

    .dash-number{
        font-size:28px;
    }

    .dash-label{
        font-size:13px;
    }

    .dash-icon{
        font-size:24px;
    }

    .dash-stat-card .card-footer{
        padding:10px 15px;
    }

    .dash-stat-card .card-footer a{
        font-size:13px;
    }
}

/* =========================
   PANELS
========================= */
.dashboard-panel{
    border-radius:18px;
    border:none;
    box-shadow:0 8px 22px rgba(0,0,0,0.08);
    overflow:hidden;
}

.dashboard-panel .card-header{
    background:#fff;
    border-bottom:1px solid #eee;
    font-weight:700;
}

.fa-bell{
    color:#0b3c5d;
    font-size:20px;
}

.dropdown button:hover{
    background:#f8f9fa;
}
/* =========================
   DASH CARDS
========================= */
.dropdown-item {
    font-size: 14px;
}

.dropdown-item:hover {
    background: #f8f9fa;
}

.dash-stat-card{
    background:#ffffff !important;
    color:#0b3c5d !important;
    border:1px solid rgba(11,60,93,0.10);
    border-radius:18px;
    box-shadow:0 8px 22px rgba(0,0,0,0.08);
    min-height:165px;
    transition:0.3s ease;
}

.dash-stat-card:hover{
    background:#0b3c5d !important;
    transform:translateY(-5px);
}

.dash-stat-card:hover .dash-label,
.dash-stat-card:hover .dash-number,
.dash-stat-card:hover .dash-icon,
.dash-stat-card:hover a{
    color:#fff !important;
}

.dash-label,
.dash-number,
.dash-icon,
.dash-stat-card a{
    color:#0b3c5d !important;
}

.dash-label{
    font-size:14px;
    font-weight:700;
}

.dash-number{
    font-size:34px;
    font-weight:800;
    line-height:1;
}

.dash-icon{
    font-size:30px;
}

.dash-stat-card a{
    font-weight:700;
    text-decoration:none;
}

/* =========================
   RING
========================= */
.dash-ring-wrap{
    width:220px;
    height:220px;
    margin:auto;
    position:relative;
}

.dash-ring-center{
    position:absolute;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
}

.dash-ring-number{
    font-size:40px;
    font-weight:800;
}

/* =========================
   DARK MODE
========================= */
body.dark-mode .dashboard-panel,
body.dark-mode .dashboard-panel .card-body,
body.dark-mode .dashboard-panel .card-header{
    background:#111827 !important;
    color:#fff !important;
}

body.dark-mode .dash-ring-number{
    color:#fff;
}
</style>
@endsection

@section('scripts')
<script>
let labels = @json($months);
let salesData = @json($sales);
let orderData = @json($orders);

/* BAR CHART */
new Chart(document.getElementById("myBarChart"), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: "Sales",
            data: salesData,
            backgroundColor: "#0b3c5d",
            borderRadius: 6
        },{
            label: "Orders",
            data: orderData,
            backgroundColor: "#f4b400",
            borderRadius: 6
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false
    }
});

/* AREA CHART */
new Chart(document.getElementById("myAreaChart"), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label:"Sales Growth",
            data:salesData,
            borderColor:"#0b3c5d",
            backgroundColor:"rgba(11,60,93,0.15)",
            fill:true,
            tension:0.4
        },{
            label:"Orders Growth",
            data:orderData,
            borderColor:"#f4b400",
            backgroundColor:"rgba(244,180,0,0.15)",
            fill:true,
            tension:0.4
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false
    }
});

/* RING */
new Chart(document.getElementById("progressChart"), {
    type:'doughnut',
    data:{
        datasets:[{
            data:[
                {{ $availableProducts }},
                {{ $pendingOrders }},
                {{ $orderCount }},
                {{ $stockCount }}
            ],
            backgroundColor:[
                "#22235F",
                "#19e7b0",
                "#7A4D9F",
                "#EB68A0"
            ]
        }]
    },
    options:{
        responsive:true,
        cutout: '75%',
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endsection