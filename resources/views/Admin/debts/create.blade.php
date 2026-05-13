@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Multiple Debts</h5>
            <button type="button" class="btn btn-light btn-sm" id="addRow">
                <i class="fas fa-plus"></i> Add Row
            </button>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('debt.store') }}">
                @csrf

                <div id="debtContainer">

                    <!-- ROW -->
                    <div class="debt-row border rounded p-3 mb-3 bg-light">

                        <div class="row">

                            <!-- CUSTOMER -->
                            <div class="col-md-4 mb-2">
                                <select name="debts[0][customer_id]" class="form-control customer-select" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- AMOUNT -->
                            <div class="col-md-3 mb-2">
                                <input type="number" name="debts[0][amount]" class="form-control amount-input" placeholder="Amount" required>
                            </div>

                            <!-- DESCRIPTION -->
                            <div class="col-md-4 mb-2">
                                <input type="text" name="debts[0][description]" class="form-control" placeholder="Description">
                            </div>

                            <!-- REMOVE -->
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- AUTO DATA -->
                            <div class="col-md-12 mt-2">
                                <small>
                                    Total: <span class="total">0</span> |
                                    Paid: <span class="paid">0</span> |
                                    Remaining: <span class="remaining text-danger">0</span>
                                </small>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Debts
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

{{-- ========================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================= --}}

<script>
let index = 1;

// ADD ROW
document.getElementById('addRow').addEventListener('click', function () {

    let container = document.getElementById('debtContainer');
    let newRow = container.querySelector('.debt-row').cloneNode(true);

    let inputs = newRow.querySelectorAll('input, select');

    inputs.forEach(function(input){
        let name = input.getAttribute('name');
        name = name.replace(/\d+/, index);
        input.setAttribute('name', name);
        input.value = '';
    });

    // RESET AUTO VALUES
    newRow.querySelector('.total').innerText = '0';
    newRow.querySelector('.paid').innerText = '0';
    newRow.querySelector('.remaining').innerText = '0';

    container.appendChild(newRow);
    index++;
});


// REMOVE ROW
document.addEventListener('click', function(e){
    if(e.target.closest('.remove-row')){
        let rows = document.querySelectorAll('.debt-row');
        if(rows.length > 1){
            e.target.closest('.debt-row').remove();
        }
    }
});


// AUTO LOAD CUSTOMER DATA
document.addEventListener('change', function (e) {

    if (e.target.classList.contains('customer-select')) {

        let customerId = e.target.value;
        let row = e.target.closest('.debt-row');

        if (!customerId) return;

        fetch('/customer-debt/' + customerId)
            .then(res => res.json())
            .then(data => {

                row.querySelector('.total').innerText = data.total.toFixed(2);
                row.querySelector('.paid').innerText = data.paid.toFixed(2);
                row.querySelector('.remaining').innerText = data.remaining.toFixed(2);

            });

    }

});
</script>

@endsection