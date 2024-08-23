@extends('admin::layouts.master')

@section('content')

@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
@endif 

<div class="container-xxl flex-grow-1 container-p-y">
 
        <div class="card">

  <h5 class="card-header pb-0 text-md-start text-center">Site Management</h5>
  <a href="{{ route('sites.create') }}" class="align-right btn btn-pinned btn-primary">Add Site</a>
 <div class="card-datatable text-nowrap">
                                    <table class="table table-bordered" id="sitesdataTable" data-auto-responsive="false">
                                    <thead>
            <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col"><span class="sub-text">Created At</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Code</span></th> 
                                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                            <!-- <th class="nk-tb-col"><span class="sub-text">Email Content</span></th> -->
                                            <th class="nk-tb-col"><span class="sub-text">Action</span></th>
                                        </tr>
                                    </thead>
                                   
                                </table>
                            </div>

        </div>
        </div> 
@endsection
