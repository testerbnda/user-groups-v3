<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact " dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard | OptiKash</title>


    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />


    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}">


    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>
<style>
    .btn-primary {

        color: #fff;
        background-color: #2f358f;
        border-color: #2f358f;
    }

    a:hover {
        color: #fa7d1a;
    }

    a {
        color: #2f358f;
    }

    .app-brand-logo {
        flex-shrink: initial;
    }

    a.btn-secondary {
        color: #fff;
        background-color: #F47E20;
        border-color: #F47E20;
        box-shadow: 0 0.125rem 0.25rem 0 rgba(133, 146, 163, 0.4);
    }

    textarea {
        resize: none;
    }

    .custom-select {
    width: 100%;
    padding: 0.375rem 1.75rem 0.375rem 0.75rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 10px solid #ced4da;
    border-radius: 0.25rem;
    appearance: none;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}



.remove-bucket, .add-bucket {
    height: 35px;
}

.modal-header {
    background-color: #007bff;
    color: white;
    border-bottom: 1px solid #dee2e6;
}

.modal-title {
    font-weight: 500;
    font-size: 1.25rem;
}

.modal-body {
    padding: 1.5rem 2rem;
}

.form-label {
    font-weight: 500;
    color: #333;
    margin-bottom: 0.5rem;
}

.custom-select {
    padding: 0.5rem;
    font-size: 1rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out;
}

.bucket-entry {
    margin-bottom: 1rem;
}

#selectedBuckets .row {
    margin-bottom: 0.75rem;
}


#selectedBuckets .form-control {
    margin-top: 0.5rem;
    border-radius: 0.25rem;
}

#transferFundsForm .btn-success {
    padding: 0.5rem 1.5rem;
}

</style>

<body>
    <!-- Content -->

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin::layouts.partial.sidebar')

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('admin::layouts.partial.nav')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('admin::layouts.partial.footer')
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->


    <!-- Vendors JS -->

    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>


    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>


    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>


    <!-- <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.6/jquery.easypiechart.min.js"></script>
    
  
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
    $("#loginForm").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 5
            }
        },
        messages: {
            email: "Please enter a valid email address",
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            }
        }
    });
</script> -->



    @include('admin::layouts.partial.script')

    <!-- <script src="{{ asset('js/jquery.validate.min.js') }}"></script> -->
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <script type="text/javascript">
        $("#formAuthentication").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 5
                }
            },
            messages: {
                email: "Please enter a valid email address",
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                }
            }
        });


        $("#formCreateUser").validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 5
                },
                formValidationConfirmPass: {
                    required: true,
                    minlength: 5,

                    equalTo: "#password"
                },
                user_type: {
                    required: true
                },

            },
            messages: {
                email: "Please enter a valid email address",
                password: {
                    required: " Please enter a password",
                    minlength: " Your password must be consist of at least 5 characters"
                },
                formValidationConfirmPass: {
                    required: " Please enter a password",
                    minlength: " Your password must be consist of at least 5 characters",
                    equalTo: " Please enter the same password as entered in password field"
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "password")
                    error.insertAfter(".error-password");
                else if (element.attr("name") == "formValidationConfirmPass")
                    error.insertAfter(".error-cpassword");
                else if (element.attr("name") == "user_type")
                    error.insertAfter(".error-usertype");
                else
                    error.insertAfter(element);
            }

        });



        $("#formCreateUserUpdate").validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true
                },
                password: {

                    minlength: 5
                },
                formValidationConfirmPass: {

                    minlength: 5,

                    equalTo: "#password"
                },
                user_type: {
                    required: true
                },

            },
            messages: {
                email: "Please enter a valid email address",
                password: {
                    required: " Please enter a password",
                    minlength: " Your password must be consist of at least 5 characters"
                },
                formValidationConfirmPass: {
                    required: " Please enter a password",
                    minlength: " Your password must be consist of at least 5 characters",
                    equalTo: " Please enter the same password as entered in password field"
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "password")
                    error.insertAfter(".error-password");
                else if (element.attr("name") == "formValidationConfirmPass")
                    error.insertAfter(".error-cpassword");
                else if (element.attr("name") == "user_type")
                    error.insertAfter(".error-usertype");
                else
                    error.insertAfter(element);
            }

        });



        $("#formCreateSite").validate({
            rules: {
                site_name: {
                    required: true
                },
                site_code: {
                    required: true,
                    maxlength: 5,
                }


            }

        });




        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch (type) {
                case 'info':
                    toastr.info("{{ Session::get('message') }}", "info", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-full-width'
                    });
                    break;

                case 'warning':
                    toastr.warning("{{ Session::get('message') }}", "warning", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-full-width'
                    });
                    break;

                case 'success':
                    toastr.success("{{ Session::get('message') }}", "success", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-full-width'
                    });
                    break;

                case 'error':
                    toastr.error("{{ Session::get('message') }}", "error", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-full-width'
                    });
                    break;
            }
        @endif

        document.addEventListener('DOMContentLoaded', function() {
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
            userSearchInput.addEventListener('input', function() {
                const query = this.value.trim();
                fetch(`/admin/searchuser?query=${query}`)
                    .then(response => response.json())
                    .then(users => {
                        userSuggestions.innerHTML = '';
                        if (users.users.length) {
                            users.users.forEach(user => {
                                if (!selectedUsers.has(user.id + '')) {
                                    const suggestionItem = document.createElement('button');
                                    suggestionItem.type = 'button';
                                    suggestionItem.classList.add('list-group-item',
                                        'list-group-item-action');
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
            document.addEventListener('click', function(event) {
                if (!userSearchInput.contains(event.target) && !userSuggestions.contains(event.target)) {
                    userSuggestions.innerHTML = '';
                }
            });
            const nextStepBtn = document.getElementById('nextStepBtn');
            const backBtn = document.getElementById('backBtn');
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');

            nextStepBtn.addEventListener('click', function() {
                var groupName = document.getElementById('groupName').value;
                if (groupName.trim() === '') {
                    alert('Group Name is required!');
                    return;
                }
                step1.style.display = 'none';
                step2.style.display = 'block';
            });

            backBtn.addEventListener('click', function() {
                step1.style.display = 'block';
                step2.style.display = 'none';
            });
        });
    </script>

</body>



</html>
