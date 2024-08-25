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
  <!-- <a href="{{ route('user.create') }}" class="align-right btn btn-pinned btn-primary">Add User</a> -->
 <div class="card-datatable text-nowrap">
                                    <table class="table table-bordered" id="groupusersdataTable" data-auto-responsive="false">
                                    <thead>
            <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col"><span class="sub-text">Joined At</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Email</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Mobile</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                            <!-- <th class="nk-tb-col"><span class="sub-text">Email Content</span></th> -->
                                        </tr>
                                    </thead>
                                   
                                </table>
                            </div>

        </div>
        </div> 
@endsection
