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
            @if (count($errors) > 0)
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
                    <label class="form-label" for="site_name">Name</label> 
                    {!! Form::text('name', null, array('id'=>'name','class' => 'form-control')) !!}
                </div>
 
                <div class="col-md-6">
                    <div class="input-group input-group-merge  ">
                        <label class="form-label" for="user_type">Status</label>
                        <select class="form-select" name="status" id="status">  
                            <option value="1">Active</option> 
                            <option value="0">Inactive</option> 
                        </select>
                    </div>
                </div>
                <div class="col-12">
                <button type="submit" name="submitButton" class="btn btn-primary">Save</button>
                <a  href="{{ route('groups.list') }}"   class="btn btn-secondary">Cancel</a>
                </div>
                {!! Form::close() !!}
            </div>
          </div>
        </div>
        <!-- /FormValidation -->
      </div>      
        </div> 
@endsection
