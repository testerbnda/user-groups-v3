@extends('admin::layouts.master')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Card for Total Balance and Total Buckets -->
    <div class="row mb-4">
        <!-- Total Balance Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Balance</h5>
                    <p class="card-text fs-3" id="totalBalance">{{ number_format($totalBalance, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Buckets Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Buckets</h5>
                    <p class="card-text fs-3" id="totalBuckets">{{ $bucketCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recievable Section -->
    <div class="card">
        <h5 class="card-header pb-0 text-md-start text-center">Recievables Bucket</h5>
        <a href="{{ route('recievables.create') }}" class="align-right btn btn-pinned btn-primary">Add New</a>
        <div class="card-datatable text-nowrap">
            <table class="table table-bordered" id="recievablesdataTable" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span class="sub-text">Created</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Bucket Name</span></th> 
                        <th class="nk-tb-col"><span class="sub-text">Vid A/C</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Balance</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Action</span></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@endsection
