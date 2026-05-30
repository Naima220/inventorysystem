@extends('layouts.admin_master')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>📊 Annual Financial Report (Xisaab Xidhka Sanadlaha)</h3>
        
        <!-- Year Filter Form -->
        <form method="GET" action="" class="d-flex">
            <select name="year" class="form-select me-2" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                        Sanadka {{ $y }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary d-none">Filter</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white text-center shadow">
                <div class="card-body">
                    <h6>Total Income</h6>
                    <h3>${{ number_format($totalIncome, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white text-center shadow">
                <div class="card-body">
                    <h6>Total Salaries</h6>
                    <h3>${{ number_format($totalSalaries, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark text-center shadow">
                <div class="card-body">
                    <h6>Total Expenses</h6>
                    <h3>${{ number_format($totalExpenses, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $profit >= 0 ? 'bg-primary' : 'bg-secondary' }} text-white text-center shadow">
                <div class="card-body">
                    <h6>Net Profit</h6>
                    <h3>${{ number_format($profit, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Monthly Trend ({{ $year }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="financialChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Quick Summary List -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Yearly Outcome Breakdown</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Outcome (Kharashka Guud)
                            <span class="badge bg-danger rounded-pill">${{ number_format($totalOutcome, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-muted small">
                            ├─ Product Purchases (Alaab iibsi)
                            <span>${{ number_format($totalPurchases, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-muted small">
                            ├─ Employee Salaries (Mushaar)
                            <span>${{ number_format($totalSalaries, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-muted small">
                            ├─ General Expenses (Kharash Kale)
                            <span>${{ number_format($totalExpenses, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center mt-2 border-top">
                            Total Income (Dakhli)
                            <span class="badge bg-success rounded-pill">${{ number_format($totalPayments, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Data Table -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">Monthly Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th>Income (Dakhli)</th>
                            <th>Outcome (Kharash)</th>
                            <th>Profit (Faa'iido)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyData as $data)
                        <tr>
                            <td class="fw-bold">{{ $data['month'] }}</td>
                            <td class="text-success">${{ number_format($data['income'], 2) }}</td>
                            <td class="text-danger">${{ number_format($data['outcome'], 2) }}</td>
                            <td class="{{ $data['profit'] >= 0 ? 'text-primary fw-bold' : 'text-danger fw-bold' }}">
                                ${{ number_format($data['profit'], 2) }}
                            </td>
                            <td>
                                @if($data['profit'] > 0)
                                    <span class="badge bg-success">Profit</span>
                                @elseif($data['profit'] < 0)
                                    <span class="badge bg-danger">Loss</span>
                                @else
                                    <span class="badge bg-secondary">Break Even</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        const monthlyData = @json($monthlyData);

        const labels = monthlyData.map(data => data.month);
        const incomeData = monthlyData.map(data => data.income);
        const outcomeData = monthlyData.map(data => data.outcome);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Income ($)',
                        data: incomeData,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Outcome ($)',
                        data: outcomeData,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
