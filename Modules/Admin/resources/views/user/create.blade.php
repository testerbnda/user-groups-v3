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
            <h5 class="card-header">Add User</h5>
            <div class="card-body">
                {!! Form::open(array('route' => 'user.store','method'=>'POST','enctype'=>'multipart/form-data','class'=>'row g-6','id'=>'formCreateUser')) !!}
                
                <div class="col-md-6">
                    <label class="form-label" for="name">Full Name</label> 
                    {!! Form::text('name', null, array('id'=>'name','class' => 'form-control')) !!}
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-merge error-usertype">
                        <label class="form-label" for="user_type">User Type</label>
                        <select class="form-select select2" name="user_type" id="user_type"> 
                            <option value="">Select</option> 
                            <option value="user">User</option> 
                            <option value="admin">Admin</option> 
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="email">Email</label>
                  {!! Form::text('email', null, array('class' => 'form-control','id'=>'email')) !!}
                </div>

                <div class="col-md-6 "> 
                <label class="form-label" for="country_code">Country Code</label>
                    <select class="form-select select2" name="country_code" id="country_code"> 
                        <option value="91">(+91)&nbsp;&nbsp;India</option>
                        @foreach (get_countries() as $countriesKey => $countriesValue)
                        <option value="{{$countriesValue->code}}">(+{{$countriesValue->code}})&nbsp;&nbsp;{{$countriesValue->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6"> 
                    <label class="form-label" for="mobile_no">Mobile</label>
                    {!! Form::text('mobile_no',null, array('id'=>'mobile_no','class' => 'form-control','oninput'=>'this.value =  !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null')) !!}
                </div>

                <div class="col-md-6">
                    <div class="input-group input-group-merge  ">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select" name="status" id="status">  
                            <option value="1">Active</option> 
                            <option value="0">Inactive</option> 
                        </select>
                    </div>
                </div>
            
      
                <div class="col-md-6">
                  <div class="form-password-toggle">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge error-password">
                      <input class="form-control" type="password" id="password" name="password" placeholder="············" aria-describedby="multicol-password2">
                      <span class="input-group-text cursor-pointer " id="multicol-password2"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-password-toggle">
                    <label class="form-label" for="formValidationConfirmPass">Confirm Password</label>
                    <div class="input-group input-group-merge error-cpassword">
                      <input class="form-control" type="password" id="formValidationConfirmPass" name="formValidationConfirmPass" placeholder="············" aria-describedby="multicol-confirm-password2">
                      <span class="input-group-text cursor-pointer  " id="multicol-confirm-password2"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
                </div>
       
                <div class="col-12">
                <button type="submit" name="submitButton" class="btn btn-primary">Save</button>
                <a  href="{{ route('user.index') }}"   class="btn btn-secondary">Cancel</a>
                </div>
                {!! Form::close() !!}
            </div>
          </div>
        </div>
        <!-- /FormValidation -->
      </div>
      
        </div> 
@endsection
