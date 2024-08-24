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

                <!-- User Management Section -->
                <div class="col-12 mt-7">
                    <h5 class="mb-2">Manage Group Members</h5>

                    <!-- Search Users -->
                    <div class="mb-3">
                        <label for="userSearch" class="form-label">Search Users to Add</label>
                        <input type="text" class="form-control" id="userSearch" placeholder="Search users by name...">
                        <div id="userSuggestions" class="list-group position-absolute w-100 mt-1"></div>
                    </div>

                    <!-- Selected Users -->
                    <div class="mb-3">
                        <label class="form-label">Current Members</label>
                        <div id="selectedUsers">
                            @foreach($group->users as $user)
                                <span class="badge bg-primary me-2 mb-2" data-user-id="{{ $user->id }}">
                                    {{ $user -> name }}
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
{{ $user->name }}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userSearchInput = document.getElementById('userSearch');
    const userSuggestions = document.getElementById('userSuggestions');
    const selectedUsersContainer = document.getElementById('selectedUsers');
    const userIdsInput = document.getElementById('userIdsInput');

    let selectedUsers = new Map();

    // Initialize with existing group users
    selectedUsersContainer.querySelectorAll('.badge').forEach(badge => {
        const userId = badge.dataset.userId;
        selectedUsers.set(userId, badge.textContent.trim());
    });

    // Search Users
    userSearchInput.addEventListener('input', function () {
        const query = this.value.trim();
        fetch(`/admin/searchuser?query=${query}`)
            .then(response => response.json())
            .then(users => {
                userSuggestions.innerHTML = '';

                if (users.users.length) {
                    users.users.forEach(user => {
                        if (!selectedUsers.has(user.id)) {
                            const suggestionItem = document.createElement('button');
                            suggestionItem.type = 'button';
                            suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                            suggestionItem.textContent = user.name;
                            suggestionItem.addEventListener('click', () => {
                                addUserToSelected(user.id, user.name);
                                userSuggestions.innerHTML = '';
                                userSearchInput.value = '';
                            });
                            userSuggestions.appendChild(suggestionItem);
                        }
                    });
                } else {
                    const noResultItem = document.createElement('div');
                    noResultItem.classList.add('list-group-item');
                    noResultItem.textContent = 'No users found.';
                    userSuggestions.appendChild(noResultItem);
                }
            });
    });

    // Add User to Selected List
    function addUserToSelected(userId, userName) {
        if (selectedUsers.has(userId)) return;

        selectedUsers.set(userId, userName);

        const userBadge = document.createElement('span');
        userBadge.classList.add('badge', 'bg-primary', 'me-2', 'mb-2');
        userBadge.textContent = userName;
        userBadge.dataset.userId = userId;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.classList.add('btn-close', 'btn-close-white', 'ms-2');
        removeBtn.ariaLabel = 'Remove';
        removeBtn.dataset.userId = userId;
        removeBtn.addEventListener('click', () => {
            selectedUsers.delete(userId);
            userBadge.remove();
            updateUserIdsInput();
        });

        userBadge.appendChild(removeBtn);
        selectedUsersContainer.appendChild(userBadge);
        updateUserIdsInput();
    }

    // Handle User Removal
    selectedUsersContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-user')) {
            const userId = event.target.dataset.userId;
            selectedUsers.delete(userId);
            event.target.closest('.badge').remove();
            updateUserIdsInput();
        }
    });

    // Update the hidden input with selected user IDs
    function updateUserIdsInput() {
        userIdsInput.value = Array.from(selectedUsers.keys()).join(',');
    }

    // Close suggestions on outside click
    document.addEventListener('click', function (event) {
        if (!userSearchInput.contains(event.target) && !userSuggestions.contains(event.target)) {
            userSuggestions.innerHTML = '';
        }
    });
});
</script>