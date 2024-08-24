@extends('admin::layouts.master')

@section('content')

@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
@endif 

<div class="container-xxl flex-grow-1 container-p-y">
 
        <div class="card">

  <h5 class="card-header pb-0 text-md-start text-center">Group Management</h5>
  <a href="{{ route('groups.create') }}" class="align-right btn btn-pinned btn-primary">Add Group</a>
 <div class="card-datatable text-nowrap">
                                    <table class="table table-bordered" id="groupsdataTable" data-auto-responsive="false">
                                    <thead>
            <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col"><span class="sub-text">Created</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Name</span></th> 
                                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Action</span></th>
                                        </tr>
                                    </thead>
                                   
                                </table>
                            </div>

        </div>
        </div> 
@endsection
