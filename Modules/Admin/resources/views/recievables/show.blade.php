@extends('admin::layouts.master')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-3">Transfer Funds</h1>
        </div>
    </div>

    <!-- Card for Bucket Balance -->
    <div class="row mb-4">
        <!-- Bucket Balance Card -->
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">Bucket Name: {{ $bucket->name }}</h5>
                    <p class="card-text fs-3">Balance: {{ number_format($balance, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Funds Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-secondary">
                <div class="card-body">
                    <h5 class="card-title">Transfer Funds</h5>
                    <form id="transferFundsForm">
                        <div class="mb-3">
                            <label class="form-label">Select Buckets to Transfer To</label>
                            <div id="bucketCheckboxes">
                                @foreach($buckets as $b)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input bucket-checkbox" type="checkbox" value="{{ $b->id }}" id="bucket{{ $b->id }}">
                                        <label class="form-check-label fw-bold text-uppercase h5" for="bucket{{ $b->id }}">
                                            {{ $b->name }}
                                        </label>
                                    </div>
                                    <div class="mb-3 bucket-amount" id="amount{{ $b->id }}" style="display:none;">
                                        <label for="amountInput{{ $b->id }}" class="form-label">Amount to Transfer to {{ $b->name }}</label>
                                        <input type="number" class="form-control amount-input" id="amountInput{{ $b->id }}" name="{{ $b->id }}" min="1" placeholder="Enter amount">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Transfer Funds</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const bucketCheckboxes = document.querySelectorAll('.bucket-checkbox');
    const balanceElement = document.querySelector('.card-text.fs-3'); 
    let balance = parseFloat(balanceElement.textContent.replace(/[^0-9.-]+/g, '')); 
    // Function to update the balance and check for errors
    function updateBalance() {
        let totalTransferAmount = 0;
        document.querySelectorAll('.amount-input').forEach(function(input) {
            if (input.value) {
                totalTransferAmount += parseFloat(input.value);
            }
        });

        const updatedBalance = balance - totalTransferAmount;
        balanceElement.textContent = `Balance: ${updatedBalance.toFixed(2)}`;

        // Update color based on balance
        if (updatedBalance < 0) {
            balanceElement.style.color = 'red';
            document.getElementById('transferFundsForm').querySelector('button').disabled = true;
        } else {
            balanceElement.style.color = 'black';
            document.getElementById('transferFundsForm').querySelector('button').disabled = false;
        }
    }

    bucketCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const amountDiv = document.getElementById('amount' + checkbox.value);
            if (checkbox.checked) {
                amountDiv.style.display = 'block';
            } else {
                amountDiv.style.display = 'none';
                amountDiv.querySelector('.amount-input').value = '';
            }
            updateBalance(); // Update balance when checkbox changes
        });
    });

    document.querySelectorAll('.amount-input').forEach(function(input) {
        input.addEventListener('input', function() {
            updateBalance(); // Update balance when input value changes
        });
    });

    document.getElementById('transferFundsForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const selectedAmounts = {};
        document.querySelectorAll('.amount-input').forEach(function(input) {
            if (input.value) {
                selectedAmounts[input.name] = Number(input.value);
            }
        });
        if (Object.keys(selectedAmounts).length) {
            const bucketId = @json($bucket->id);
            
            $.ajax({
                url: `/admin/recievables/transferfunds/${bucketId}`, 
                method: 'POST',
                data: {
                    payoutBuckets: selectedAmounts,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    console.log('Transfer successful:', response);
                },
                error: function(xhr) {
                    console.error('Transfer failed:', xhr.responseText);
                }
            });
        }
    });
});

</script>
