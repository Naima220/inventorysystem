@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4 text-warning">📊 General Reports</h2>

    {{-- Reports Cards Grid --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        {{-- Orders --}}
        <div class="col mb-4">
            <a href="{{ route('reports.orders') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#b6874a;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">🛒 Total Orders</h5>
                        <p class="card-text display-4">{{ $data['total_orders'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Orders Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Invoices --}}
        <div class="col mb-4">
            <a href="{{ route('reports.invoices') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#1eacee;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">📄 Total Invoices</h5>
                        <p class="card-text display-4">{{ $data['total_invoices'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Invoices Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Payments --}}
        <div class="col mb-4">
            <a href="{{ route('reports.payments') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#e0456a;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">💰 Total Payments</h5>
                        <p class="card-text display-4">{{ $data['total_payments'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Payments Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Salaries --}}
        <div class="col mb-4">
            <a href="{{ route('reports.salaries') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#843b62;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">💼 Total Salaries</h5>
                        <p class="card-text display-4">{{ $data['salary_count'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Salaries Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Expenses --}}
        <div class="col mb-4">
            <a href="{{ route('reports.expenses') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#f67e7d;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">📉 Total Expenses</h5>
                        <p class="card-text display-4">{{ $data['total_expenses'] }} $</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Expenses Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Customers --}}
        <div class="col mb-4">
            <a href="{{ route('reports.customers') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#110e29;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">👥 Total Customers</h5>
                        <p class="card-text display-4">{{ $data['total_customers'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Customers Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Employees --}}
        <div class="col mb-4">
            <a href="{{ route('reports.employees') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#e98ca4;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">👔 Total Employees</h5>
                        <p class="card-text display-4">{{ $data['total_employees'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Employees Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Products --}}
        <div class="col mb-4">
            <a href="{{ route('reports.products') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#952237;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">📦 Total Products</h5>
                        <p class="card-text display-4">{{ $data['total_products'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Products Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Suppliers --}}
        <div class="col mb-4">
            <a href="{{ route('reports.suppliers') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#355c7d;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">🏭 Total Suppliers</h5>
                        <p class="card-text display-4">{{ $data['total_suppliers'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Suppliers Report</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Debts --}}
        <div class="col mb-4">
            <a href="{{ route('reports.debts') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="background:#7b2cbf;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">💳 Total Debts</h5>
                        <p class="card-text display-4">{{ $data['total_debts'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <small class="text-warning">View Debt Report</small>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection