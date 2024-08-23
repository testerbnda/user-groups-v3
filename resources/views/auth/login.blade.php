@extends('layouts.app')

@section('content')
<div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card px-sm-6 px-0">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
              <div class="app-brand justify-content-center">
                <img src="{{ asset('assets/img/logo/opti-logo.png') }}" class="">
              </div>
              </div>
                 @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
              <!-- /Logo -->
              <h4 class="mb-1">Welcome to Optikash! 👋</h4>
              <p class="mb-6">Please sign-in to your account</p>

              <form method="POST" action="{{ route('login') }}" id="formAuthentication" class="mb-6" >
                @csrf
                <div class="mb-6">
                  <label for="email" class="form-label">Email</label>
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="emailHelp" autofocus>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-6 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                   
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="off">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                     
                </div>
               
                <div class="mb-6">
                  <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Login') }}</button>
                </div>
              </form>

              <!-- <p class="text-center">
                <span>New on our platform?</span>
                <a href="{{ url('register') }}">
                  <span>Create an account</span>
                </a>
              </p> -->
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->
 
        @endsection
