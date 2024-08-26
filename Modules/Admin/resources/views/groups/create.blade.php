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
                            <input type="text" class="form-control" id="groupName" name="name" required>
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
                            <label for="username" class="form-label">Search Users</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div id="userSuggestions" class="user-suggestions mt-2"></div>
                        </div>
                        <div class="col-md-6 mb-3" id="selectedUsers">
                            <!-- Selected users will be displayed here -->
                        </div>
                        <input type="hidden" id="selectedUserIds" name="user_ids" value="">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nextStepBtn = document.getElementById('nextStepBtn');
        const backBtn = document.getElementById('backBtn');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const usernameInput = document.getElementById('username');
        const userSuggestions = document.getElementById('userSuggestions');
        const selectedUserIdsInput = document.getElementById('selectedUserIds');
        const selectedUsersDiv = document.getElementById('selectedUsers');

        const selectedUserIds = new Set();

        nextStepBtn.addEventListener('click', function () {
            step1.style.display = 'none';
            step2.style.display = 'block';
        });

        backBtn.addEventListener('click', function () {
            step1.style.display = 'block';
            step2.style.display = 'none';
        });

        usernameInput.addEventListener('input', function () {
            fetchUsers(usernameInput.value);
        });

        function fetchUsers(query) {
            fetch(`/admin/searchuser?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    userSuggestions.innerHTML = '';
                    data.users.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'user-suggestion-item';
                        div.textContent = user.name;
                        div.dataset.userId = user.id;
                        div.addEventListener('click', function () {
                            selectUser(user.id, user.name);
                        });
                        userSuggestions.appendChild(div);
                    });
                });
        }

        function selectUser(userId, userName) {
            if (!selectedUserIds.has(userId)) {
                selectedUserIds.add(userId);
                const div = document.createElement('div');
                div.className = 'badge bg-primary text-light me-1 selected-user-item';
                div.textContent = userName;
                div.dataset.userId = userId;
                div.addEventListener('click', function () {
                    removeUser(userId, div);
                });
                selectedUsersDiv.appendChild(div);
                updateSelectedUserIds();
            }
        }

        function removeUser(userId, element) {
            selectedUserIds.delete(userId);
            element.remove();
            updateSelectedUserIds();
        }

        function updateSelectedUserIds() {
            selectedUserIdsInput.value = Array.from(selectedUserIds).join(',');
        }

        document.getElementById('createGroupForm').addEventListener('submit', function () {
            updateSelectedUserIds(); // Ensure the hidden input is updated before form submission
        });
    });
</script>
