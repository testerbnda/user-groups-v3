// import './bootstrap';
// import 'laravel-datatables-vite';

document.addEventListener('DOMContentLoaded', function () {
    const editGroupButtons = document.querySelectorAll('.edit-group-btn');
    const editGroupModal = new bootstrap.Modal(document.getElementById('editGroupModal'));
    const editGroupForm = document.getElementById('editGroupForm');
    const groupNameInput = document.getElementById('groupName');
    const groupStatusSelect = document.getElementById('groupStatus');
    const userSearchInput = document.getElementById('userSearch');
    const userSuggestions = document.getElementById('userSuggestions');
    const selectedUsersContainer = document.getElementById('selectedUsers');
    const userIdsInput = document.getElementById('userIdsInput');

    let selectedUsers = new Map();
    editGroupButtons.forEach(button => {
        button.addEventListener('click', function () {
            const group = JSON.parse(this.dataset.group);
            groupNameInput.value = group.name;
            groupStatusSelect.value = group.status ? '1' : '0';
            selectedUsers.clear();
            selectedUsersContainer.innerHTML = '';
            group.users.forEach(user => {
                addUserToSelected(user.id, user.name);
            });
            editGroupForm.action = `/groups/${group.id}/update`;
            editGroupModal.show();
        });
    });

    // Search Users
    userSearchInput.addEventListener('input', function () {
        const query = this.value.trim();
        fetch(`/users?query=${query}`)
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
        removeBtn.addEventListener('click', () => {
            selectedUsers.delete(userId);
            userBadge.remove();
        });

        userBadge.appendChild(removeBtn);
        selectedUsersContainer.appendChild(userBadge);
    }

    // Handle Form Submission
    editGroupForm.addEventListener('submit', function (event) {

        userIdsInput.value = Array.from(selectedUsers.keys());
    });

    // Close suggestions on outside click
    document.addEventListener('click', function (event) {
        if (!userSearchInput.contains(event.target) && !userSuggestions.contains(event.target)) {
            userSuggestions.innerHTML = '';
        }
    });
});

$(document).ready(function () {
    $('#groups-table').DataTable()
})