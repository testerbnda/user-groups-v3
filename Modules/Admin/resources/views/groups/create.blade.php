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
                    <h5 class="card-header">Add Group</h5>
                    <div class="card-body">
                        <form id="createGroupForm" action="{{ route('groups.store') }}" method="POST">
                            @csrf
                            <div id="step1">
                                <div class="col-md-6 mb-3">
                                    <label for="groupName" class="form-label">Group Name</label>
                                    <input type="text" id="groupName" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="groupStatus" class="form-label">Status</label>
                                    <select class="form-select" id="groupStatus" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <input type="hidden" name="site_id" value="1">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="nextStepBtn">Next</button>
                                </div>
                            </div>
                            <div id="step2" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label for="userSearch" class="form-label">Search Users</label>
                                    <input type="text" class="form-control" id="userSearch" name="username">
                                    <div id="userSuggestions" class="list-group mt-2"></div>
                                </div>
                                <div class="col-md-6 mb-3" id="selectedUsers">
                                    <!-- Selected users will be displayed here -->
                                </div>
                                <input type="hidden" id="userIdsInput" name="user_ids" value="">
                                <div class="col-12 modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" id="backBtn">Back</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /FormValidation -->
        </div>
    </div>

@endsection
