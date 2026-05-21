@extends('layouts.admin_master')

@section('content')

<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">
 
                <div class="card-header bg-primary text-white text-center">
                   
                <h5 class="mb-0"> @include('components.back-button')
                        💰 Pay Debt         
                                 
                    </h5>
                     
                </div>
        
                <div class="card-body">

                    <div class="mb-3 text-center">
                        <h6 class="text-muted">Customer</h6>
                        <h4 class="fw-bold">
                            {{ $debt->customer->name }}
                        </h4>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <p><strong>Total Debt:</strong> {{ number_format($debt->amount, 2) }}</p>

                        <p><strong>Paid:</strong> {{ number_format($debt->payments->sum('paid_amount'), 2) }}</p>

                        <p class="text-danger">
                            <strong>Remaining:</strong>
                            {{ number_format($debt->amount - $debt->payments->sum('paid_amount'), 2) }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('debt.pay.store', $debt->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Enter Payment Amount</label>
                            <input type="number"
                                   step="0.01"
                                   name="amount"
                                   class="form-control form-control-lg"
                                   placeholder="e.g 50.00"
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                ✅ Pay Now
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

@endsection