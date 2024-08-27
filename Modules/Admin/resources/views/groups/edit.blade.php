@extends('admin::layouts.master')

@section('content')

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif 

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div> 
    <div class="row">
        <!-- FormValidation -->
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Edit Group</h5>
                <div class="card-body">
                    {!! Form::model($group, ['method' => 'patch','route' => ['groups.update', $group->id],'enctype'=>'multipart/form-data','class'=>'row g-6','id'=>'formCreateSiteUpdate']) !!}
                    
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name</label> 
                        {!! Form::text('name', null, ['id'=>'name','class' => 'form-control']) !!}
                    </div>
     
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" name="status" id="status">  
                                <option value="1" {{ $group->status == 1 ? 'selected' : '' }}>Active</option> 
                                <option value="0" {{ $group->status == 0 ? 'selected' : '' }}>Inactive</option> 
                            </select>
                        </div>
                    </div>

                    <!-- User Management Section -->
                    <div class="col-12 mt-3">
                        <h5 class="mb-2">Manage Group Members</h5>

                        <!-- Search Users -->
                        <div class="mb-3">
                            <label for="userSearch" class="form-label">Search Users to Add</label>
                            <input type="text" class="form-control" id="userSearch" placeholder="Search users by name...">
                            <div id="userSuggestions" class="list-group mt-2"></div>
                        </div>

                        <!-- Selected Users -->
                        <div class="mb-3">
                            <label class="form-label">Current Members</label>
                            <div id="selectedUsers" class="d-flex flex-wrap">
                                @foreach($group->users as $user)
                                    <span class="badge bg-primary me-2 mb-2" data-user-id="{{ $user->id }}">
                                        {{ $user->name }}
                                        <button type="button" class="btn-close btn-close-white ms-2 remove-user" aria-label="Remove" data-user-id="{{ $user->id }}"></button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="user_ids" id="userIdsInput" value="{{ $group->users->pluck('id')->join(',') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" name="submitButton" class="btn btn-primary">Save</button>
                        <a href="{{ route('groups.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <!-- /FormValidation -->
    </div>      
</div>
@endsection
