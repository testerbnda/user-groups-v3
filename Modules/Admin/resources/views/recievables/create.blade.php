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
                    <h5 class="card-header">Add Bucket</h5>
                    <div class="card-body">
                        <form id="createGroupForm" action="{{ route('recievables.store') }}" method="POST">
                            @csrf
                            <div class="col-md-6 mb-3">
                                <label for="bucketName" class="form-label">Bucket Name</label>
                                <input type="text" id="" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purpose" class="form-label">Purpose</label>
                                <input type="text" id="purpose" name="purpose" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea rows = 10 cols = 10 id="description" name="description" class="form-control" required> </textarea>   
                            </div>
                            <div class="col-12 modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /FormValidation -->
        </div>
    </div>

@endsection
