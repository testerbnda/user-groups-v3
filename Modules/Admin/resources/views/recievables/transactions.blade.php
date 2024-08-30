@extends('admin::layouts.master')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-3"></h1>
        </div>
    </div>
    <!-- Card for Total Balance and Total Buckets -->
    <div class="row mb-3">
        <!-- Total Balance Card -->
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
        <a href="#" class="align-right btn btn-pinned btn-primary">Transfer Funds</a>
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

@endsection
