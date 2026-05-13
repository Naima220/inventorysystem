<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>MineMart_Management_System</title>

    <link href="{{ asset('backend') }}/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --sidebar-bg: #143a59;
            --sidebar-bg-grad: #143a59;
            --topbar-bg: #ffffff;
            --page-bg: #f4f7f6;
            --primary-blue: #0d6efd;
            --sidebar-text: #ffffff;
            --sidebar-text-muted: rgba(255, 255, 255, 0.7);
            --sidebar-hover: rgba(255, 255, 255, 0.15);
        }

        body {
            background-color: var(--page-bg);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding-top: 56px; /* Space for fixed topbar */
        }

        /* TOPBAR */
        .sb-topnav.navbar {
            height: 56px;
            background-color: var(--topbar-bg) !important;
            border-bottom: 1px solid rgba(0,0,0,.05);
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
            z-index: 1030;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
        }

        .sb-topnav .navbar-brand {
            width: 250px;
            font-weight: 700;
            color: #143a59 !important;
            padding-left: 25px;
            margin: 0;
        }

        /* LAYOUT STRUCTURE */
        #layoutSidenav {
            display: flex;
            min-height: calc(100vh - 56px);
        }

        #layoutSidenav_nav {
            flex-basis: 250px;
            flex-shrink: 0;
            background: var(--sidebar-bg-grad);
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        #layoutSidenav_content {
            flex-grow: 1;
            margin-left: 250px; /* Offset for fixed sidebar */
            min-width: 0;
            display: flex;
            flex-direction: column;
            padding: 25px;
            transition: margin-left 0.3s ease;
        }

        /* SIDEBAR NAV */
        .sb-sidenav {
            height: 100%;
            overflow-y: auto;
        }

        .sb-sidenav-menu-heading {
            padding: 24px 25px 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sidebar-text-muted);
        }

        .sb-sidenav .nav-link {
            display: flex;
            align-items: center;
            padding: 13px 25px;
            color: var(--sidebar-text) !important;
            text-decoration: none;
            font-size: 0.92rem;
            transition: background 0.2s;
            position: relative;
        }

        .sb-sidenav .nav-link:hover {
            background-color: var(--sidebar-hover);
        }

        .sb-sidenav .sb-nav-link-icon {
            margin-right: 18px;
            width: 22px;
            text-align: center;
            font-size: 1.05rem;
        }

        /* DROPDOWN ARROWS */
        .sb-sidenav .sb-sidenav-collapse-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }

        /* Point RIGHT by default (collapsed) */
        .sb-sidenav .nav-link.collapsed .sb-sidenav-collapse-arrow {
            transform: rotate(-90deg);
        }

        /* Point DOWN when open (not collapsed) */
        .sb-sidenav .nav-link:not(.collapsed) .sb-sidenav-collapse-arrow {
            transform: rotate(0deg);
        }

        /* SUBMENU */
        .sb-sidenav-menu-nested {
            padding-left: 30px;
            background: rgba(0,0,0,0.08);
        }

        .sb-sidenav-menu-nested .nav-link {
            padding: 10px 20px;
            font-size: 0.88rem;
            color: var(--sidebar-text-muted) !important;
        }

        .sb-sidenav-menu-nested .nav-link:hover {
            color: var(--sidebar-text) !important;
        }

        /* TOGGLE BEHAVIOR */
        body.sb-sidenav-toggled #layoutSidenav_nav {
            transform: translateX(-250px);
        }

        body.sb-sidenav-toggled #layoutSidenav_content {
            margin-left: 0;
        }

        /* RESPONSIVE */
        @media (max-width: 991.98px) {
            #layoutSidenav_nav {
                transform: translateX(-250px);
                position: fixed; /* Ensure it stays fixed on mobile too */
                height: 100vh;
                top: 0; /* Cover full height on mobile */
                box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            }
            #layoutSidenav_content {
                margin-left: 0;
                width: 100%;
            }
            body.sb-sidenav-toggled #layoutSidenav_nav {
                transform: translateX(0);
            }
            body.sb-sidenav-toggled #layoutSidenav_content {
                margin-left: 0;
            }
            /* Backdrop when sidebar is open */
            #layoutSidenav.sb-sidenav-toggled::before {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: block;
            }
        }

        /* Prevent scrolling when sidebar is open on mobile */
        @media (max-width: 991.98px) {
            body.sb-sidenav-toggled {
                overflow: hidden;
            }
        }

        /* DARK MODE (PRESERVE) */
        body.dark-mode {
            --page-bg: #0f172a;
            --topbar-bg: #1e293b;
            color: #f1f5f9;
        }
        body.dark-mode .sb-topnav.navbar {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        body.dark-mode .sb-topnav .navbar-brand {
            color: #ffffff !important;
        }
    </style>
</head>

<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-light">
    <a class="navbar-brand" href="{{ in_array(request()->getHost(), config('tenancy.central_domains')) ? route('superadmin.dashboard') : route('dashboard') }}">Market system</a>

    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <form class="form-inline ml-auto mr-2 my-2">
        <div class="input-group d-none d-md-flex">
            <input class="form-control" type="text" placeholder="Search for..." />
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <button id="darkModeToggle" class="btn btn-outline-dark d-none d-md-inline-block ml-2" type="button">
            <i class="fas fa-moon"></i> Dark Mode
        </button>

        <button id="darkModeToggleMobile"
                class="btn btn-outline-dark d-inline-block d-md-none ml-2"
                type="button">
            <i class="fas fa-moon"></i>
        </button>
    </form>

    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" data-toggle="dropdown">
                <i class="fas fa-user fa-fw"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                @if(auth()->user()->hasRole('super_admin'))
                    <a class="dropdown-item" href="{{ route('superadmin.profile.edit') }}">
                        <i class="fas fa-id-card fa-sm fa-fw mr-2 text-gray-400"></i> Profile Settings
                    </a>
                    <a class="dropdown-item" href="{{ route('superadmin.profile.password') }}">
                        <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i> Change Password
                    </a>
                    <div class="dropdown-divider"></div>
                @endif

                <a class="dropdown-item" href="{{ route('activity.logs') }}">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log
                </a>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        Logout
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link" href="{{ in_array(request()->getHost(), config('tenancy.central_domains')) ? route('superadmin.dashboard') : route('dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>

                    @if(!in_array(request()->getHost(), config('tenancy.central_domains')))
                    <div class="sb-sidenav-menu-heading">Interface</div>

                    <!-- Customers -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomers">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Customers
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseCustomers" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('add.customer') }}">New Customer</a>
                            <a class="nav-link" href="{{ route('all.customers') }}">Customers List</a>
                        </nav>
                    </div>

                    <!-- Products -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProducts">
                        <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                        Products
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseProducts" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('products.multiple.create') }}">New Product</a>
                            <a class="nav-link" href="{{ route('all.product') }}">Stock Report</a>
                            <a class="nav-link" href="{{ route('products.available') }}">Available Products</a>
                        </nav>
                    </div>

                    <!-- Orders -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOrders">
                        <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                        Orders
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseOrders" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('new.order')}}">New Order</a>
                            <a class="nav-link" href="{{ route('all.orders')}}">Orders List</a>
                            <a class="nav-link" href="{{ route('pending.orders')}}">Pending Orders</a>
                            <a class="nav-link" href="{{ route('delivered.orders')}}">Delivered Orders</a>
                        </nav>
                    </div>

                    <!-- Invoices -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoice">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                        Sales
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseInvoice" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('invoice.create') }}">New Invoice</a>
                            <a class="nav-link" href="{{ route('invoice.index') }}">Invoices List</a>
                        </nav>
                    </div>
                    @endif

                    <!-- ADMIN ONLY -->
                    @if(!in_array(request()->getHost(), config('tenancy.central_domains')))
                    @role('super_admin|Admin|admin')

                    <!-- Suppliers -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSuppliers">
                        <div class="sb-nav-link-icon"><i class="fas fa-industry"></i></div>
                        Suppliers
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseSuppliers" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('add.supplier') }}">Add Supplier</a>
                            <a class="nav-link" href="{{ route('all.suppliers') }}">All Suppliers</a>
                        </nav>
                    </div>

                    <!-- Payments -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayments">
                        <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                        Payments
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapsePayments" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            
                            <a class="nav-link" href="{{ route('payments.index') }}">All Payments</a>
                        </nav>
                    </div>

                    <!-- Debts -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDebts">
                        <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                        Debts
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseDebts" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('debt.create') }}">Add Debt</a>
                            <a class="nav-link" href="{{ route('debt.index') }}">All Debts</a>
                        </nav>
                    </div>

                    <!-- Employees -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmployees">
                        <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                        Employees
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseEmployees" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('add.employee') }}">Add Employee</a>
                            <a class="nav-link" href="{{ route('all.employees') }}">All Employees</a>
                        </nav>
                    </div>

                    <!-- Salaries -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSalaries">
                        <div class="sb-nav-link-icon"><i class="fas fa-money-bill-wave"></i></div>
                        Salaries
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseSalaries" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('add.salary') }}">Add Salary</a>
                            <a class="nav-link" href="{{ route('all.salaries') }}">All Salaries</a>
                        </nav>
                    </div>

                    <!-- Expenses -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExpenses">
                        <div class="sb-nav-link-icon"><i class="fas fa-money-bill-wave"></i></div>
                        Expenses
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseExpenses" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('expenses.create') }}">Add Expense</a>
                            <a class="nav-link" href="{{ route('expenses.index') }}">All Expenses</a>
                        </nav>
                    </div>

                    <!-- Users -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Users
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseUsers" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('admin.users.create') }}">Add User</a>
                            <a class="nav-link" href="{{ route('admin.users.index') }}">List Users</a>
                        </nav>
                    </div>

                    <a class="nav-link" href="{{ route('report.incomeOutcome') }}">
                        <div class="d-flex align-items-center">
                            <div class="sb-nav-link-icon"><i class="fa fa-chart-line"></i></div>
                            Income & Outcome Report
                        </div>
                    </a>

                    <!-- Reports -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                        Reports
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseReports" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('reports.general') }}">General Report</a>
                        </nav>
                    </div>
                    @endrole
                    @endif

                    @role('super_admin')
                    <div class="sb-sidenav-menu-heading">Settings</div>
                    <a class="nav-link" href="{{ route('superadmin.users.index') }}">
                        <div class="d-flex align-items-center">
                            <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                            Shop Users
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('superadmin.backups.index') }}">
                        <div class="d-flex align-items-center">
                            <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                            Backup Management
                        </div>
                    </a>
                    @endrole
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main class="py-4 px-3 px-md-4 page-shell">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>

        <footer class="py-3 mt-auto footer-soft">
            <div class="container-fluid px-3 px-md-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">&copy; Naima Hassan</div>
                    <div>
                        <a href="#">Privacy Policy</a> &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- CHAT BUTTON -->
<button id="chatBtn" style="position:fixed; bottom:20px; right:20px; background:#0d6efd; color:white; border:none; padding:12px 15px; border-radius:50%; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
    💬
</button>

<!-- CHAT BOX -->
<div id="chatBox" style="display:none; position:fixed; bottom:80px; right:20px; width:300px; background:white; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2); z-index:9999;">
    <div style="background:#0d6efd; color:white; padding:10px; border-radius:10px 10px 0 0;">
        Assistant
    </div>
    <div id="chatContent" style="height:200px; overflow-y:auto; padding:10px;"></div>
    <div style="display:flex;">
        <input type="text" id="msg" placeholder="Write message..." style="flex:1; border:none; padding:10px;">
        <button onclick="sendMsg()" style="background:#0d6efd; color:white; border:none; padding:10px;">Send</button>
    </div>
</div>

<!-- Scripts -->
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('backend/js/scripts.js') }}"></script>

<!-- Chart.js ONE TIME ONLY -->


<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

<script>
    const toggle1 = document.getElementById("darkModeToggle");
    const toggle2 = document.getElementById("darkModeToggleMobile");

    function setDarkModeState(enabled){
        document.body.classList.toggle("dark-mode", enabled);

        const iconHtml = enabled
            ? '<i class="fas fa-sun"></i>'
            : '<i class="fas fa-moon"></i>';

        if(toggle1) toggle1.innerHTML = iconHtml + " " + (enabled ? "Light Mode" : "Dark Mode");
        if(toggle2) toggle2.innerHTML = iconHtml;

        localStorage.setItem("darkMode", enabled ? "enabled" : "disabled");
    }

    function toggleDark(){
        setDarkModeState(!document.body.classList.contains("dark-mode"));
    }

    if(toggle1) toggle1.addEventListener("click", toggleDark);
    if(toggle2) toggle2.addEventListener("click", toggleDark);

    if(localStorage.getItem("darkMode") === "enabled"){
        setDarkModeState(true);
    } else {
        setDarkModeState(false);
    }

    // Robust Sidebar Toggle Fix
    $(document).ready(function() {
        const $body = $('body');
        const $sidebarToggle = $('#sidebarToggle');
        
        $sidebarToggle.on('click', function(e) {
            e.preventDefault();
            $body.toggleClass('sb-sidenav-toggled');
            
            // For the backdrop effect on layoutSidenav
            $('#layoutSidenav').toggleClass('sb-sidenav-toggled');
        });

        // Close sidebar when clicking on backdrop (mobile only)
        $(document).on('click', function(e) {
            if ($(window).width() < 992) {
                if ($body.hasClass('sb-sidenav-toggled') && 
                    !$(e.target).closest('#layoutSidenav_nav').length && 
                    !$(e.target).closest('#sidebarToggle').length) {
                    $body.removeClass('sb-sidenav-toggled');
                    $('#layoutSidenav').removeClass('sb-sidenav-toggled');
                }
            }
        });
    });
</script>
<script>
document.getElementById('chatBtn').onclick = function() {
    let box = document.getElementById('chatBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
};

function sendMsg() {
    let msg = document.getElementById('msg').value;

    // Show user message
    document.getElementById('chatContent').innerHTML += 
        "<div><b>You:</b> " + msg + "</div>";

    fetch('/chatbot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: msg })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('chatContent').innerHTML += 
            "<div><b>Bot:</b> " + data.reply + "</div>";
    });

    document.getElementById('msg').value = '';
}
</script>

@yield('scripts')
</body>
</html>