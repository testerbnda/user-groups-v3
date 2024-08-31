@extends('admin::layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-3">Transactions</h1>
            </div>
        </div>

        <!-- Card for Total Balance -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Balance</h5>
                        <p class="card-text fs-3" id="totalBalance">{{ number_format($balance, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="card">
            <h5 class="card-header pb-0 text-md-start text-center">Transactions</h5>
            <a href="#" class="align-right btn btn-pinned btn-primary" data-bs-toggle="modal"
                data-bs-target="#transferFundsModal">Transfer Funds</a>
            <div class="card-datatable text-nowrap">
                <table class="table table-bordered" id="transactionsdataTable" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Created At</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Self Account</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Party Account</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Type</span></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfer Funds Modal -->
    <div class="modal fade" id="transferFundsModal" tabindex="-1" aria-labelledby="transferFundsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferFundsModalLabel">Transfer Funds</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Transfer Funds Form -->
                    <form id="transferFundsForm">
                        <div class="mb-3">
                            <label class="form-label">Select Buckets to Transfer To</label>
                            <div id="bucketDropdowns">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <select class="custom-select bucket-select">
                                            <option value="" disabled selected>Select a bucket</option>
                                            @foreach ($buckets as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="selectedBuckets" class="mt-3">
                                    <!-- Dynamically added selected buckets will appear here -->
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Transfer Funds</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bucketDropdowns = document.getElementById('bucketDropdowns');
        const selectedBucketsDiv = document.getElementById('selectedBuckets');
        const balanceElement = document.getElementById('totalBalance');
        let balance = parseFloat(balanceElement.textContent.replace(/[^0-9.-]+/g, ''));

        function updateBalance() {
            let totalTransferAmount = 0;
            document.querySelectorAll('.amount-input').forEach(function(input) {
                if (input.value) {
                    totalTransferAmount += parseFloat(input.value);
                }
            });

            const updatedBalance = balance - totalTransferAmount;
            balanceElement.textContent = updatedBalance.toFixed(2);

            if (updatedBalance < 0) {
                balanceElement.style.color = 'red';
                document.querySelector('button[type="submit"]').disabled = true;
            } else {
                balanceElement.style.color = 'black';
                document.querySelector('button[type="submit"]').disabled = false;
            }
        }

        function removeBucketOptionFromDropdowns(bucketId) {
            document.querySelectorAll('.bucket-select option').forEach(function(option) {
                if (option.value == bucketId) {
                    option.remove();
                }
            });
        }

        function reAddBucketOptionToDropdowns(bucketId, bucketName) {
            document.querySelectorAll('.bucket-select').forEach(function(select) {
                const reAddOption = document.createElement('option');
                reAddOption.value = bucketId;
                reAddOption.textContent = bucketName;
                select.appendChild(reAddOption);
            });
        }

        function addBucketSelectDropdown() {
            const newBucketDiv = document.createElement('div');
            newBucketDiv.classList.add('row', 'mb-3', 'bucket-select-row');

            let availableBucketsOptions = '<option value="" disabled selected>Select a bucket</option>';

            @foreach ($buckets as $b)
                if (!document.getElementById('bucket{{ $b->id }}')) {
                    availableBucketsOptions +=
                        `<option value="{{ $b->id }}">{{ $b->name }}</option>`;
                }
            @endforeach

            newBucketDiv.innerHTML = `
        <div class="col-md-12">
            <select class="custom-select bucket-select">
                ${availableBucketsOptions}
            </select>
        </div>
    `;

            bucketDropdowns.appendChild(newBucketDiv);
            bucketSelectEventHandler(newBucketDiv.querySelector('.bucket-select'));
        }


        function bucketSelectEventHandler(selectElement) {
            selectElement.addEventListener('change', function() {
                const selectedBucketId = selectElement.value;
                const selectedBucketName = selectElement.options[selectElement.selectedIndex].text;

                // Check if the bucket is already selected
                if (document.getElementById('bucket' + selectedBucketId)) {
                    return; // Don't add the bucket if it's already selected
                }

                const bucketDiv = document.createElement('div');
                bucketDiv.classList.add('row', 'align-items-center', 'mb-3');
                bucketDiv.id = 'bucket' + selectedBucketId;

                bucketDiv.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">Amount to Transfer to ${selectedBucketName}</label>
                    <input type="number" class="form-control amount-input" id="amountInput${selectedBucketId}" name="${selectedBucketId}" min="1" placeholder="Enter amount">
                </div>
                <div class="col-md mt-8">
                    <button type="button" class="btn btn-outline-success add-bucket">+</button>
                    <button type="button" class="btn btn-outline-danger remove-bucket" data-bucket-id="${selectedBucketId}">-</button>
                </div>
            `;

                selectedBucketsDiv.appendChild(bucketDiv);

                // Remove the selected bucket from all dropdowns
                removeBucketOptionFromDropdowns(selectedBucketId);
                selectElement.closest('.row').remove();

                updateBalance();

                bucketDiv.querySelector('.amount-input').addEventListener('input', function() {
                    updateBalance();
                });

                bucketDiv.querySelector('.remove-bucket').addEventListener('click', function() {
                    bucketDiv.remove();
                    updateBalance();
                    reAddBucketOptionToDropdowns(selectedBucketId, selectedBucketName);
                });

                bucketDiv.querySelector('.add-bucket').addEventListener('click', function() {
                    addBucketSelectDropdown();
                });
            });
        }

        // Attach event handler to the initial select element
        bucketSelectEventHandler(document.querySelector('.bucket-select'));

        document.getElementById('transferFundsForm').addEventListener('submit', function(event) {
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
