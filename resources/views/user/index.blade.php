@extends('layout.master')
@section('title','User List')
@push('plugin-styles')
  <link href="{{ asset('build/plugins/datatables.net-bs5/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endpush

@section('content')

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
          <div class="card-body d-flex justify-content-between align-items-center mb-3">
              <h6 class="card-title mb-0">User List</h6>
              <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                  <i class="fa-solid fa-plus"></i> Create New User
              </a>
          </div>
        <div class="table-responsive">
          <table id="dataTableExample" class="table">
            <thead>
              <tr>
                <th>#Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Address</th>
                <th>Register At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editBlogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')

                <input type="password" name="fake-password" style="display:none" autocomplete="off">

                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="editUserModalLabel">Update User</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editUserRole" class="form-label">Role</label>
                        <select class="form-select select2-no-search" id="editUserRole" name="role">
                            <option value="1">Admin</option>
                            <option value="2">Customer</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editUserAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="editUserAddress" name="address" rows="6"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editUserPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editUserPassword" name="password" placeholder="Password">
                            <button class="btn btn-outline-secondary" type="button" id="editTogglePassword">
                                <i class="fa-solid fa-eye" id="editPasswordToggleIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createUserForm" method="POST" action="{{ route('user.save-user') }}" autocomplete="off">
                @csrf

                <input type="text" name="fake-name" style="display:none" autocomplete="off">
                <input type="email" name="fake-email" style="display:none" autocomplete="off">
                <input type="password" name="fake-password" style="display:none" autocomplete="off">

                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="userName" name="name" autocomplete="new-name" placeholder="Customer Name">
                        <div class="invalid-feedback"></div>
                        @error('name') <p class="text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" autocomplete="new-email" placeholder="Customer Email">
                        <div class="invalid-feedback"></div>
                        @error('email') <p class="text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Role</label>
                        <select class="form-select select2-no-search" id="userRole" name="role">
                            <option value="1">Admin</option>
                            <option value="2">Customer</option>
                        </select>
                        @error('role') <p class="text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userAddress" class="form-label">Address</label>
                        <textarea type="text" class="form-control" id="userAddress" name="address" rows="6"></textarea>
                        @error('address') <p class="text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="userPassword" name="password" autocomplete="new-password" placeholder="Password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fa-solid fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                        @error('password') <p class="text-danger">{{ $message }}</p> @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('plugin-scripts')
  <script src="{{ asset('build/plugins/datatables.net/dataTables.min.js') }}"></script>
  <script src="{{ asset('build/plugins/datatables.net-bs5/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{asset('js/moment.min.js')}}"></script>
@endpush

@push('custom-scripts')

    <script>
        $(document).ready(function () {
            $('#dataTableExample').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [[0, 'desc']],
                ajax: {
                    url: '{{ route("user.user-list") }}',
                    type: 'GET',
                },
                "columns": [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role', orderable: true, searchable: false},
                    {data: 'address', name: 'address', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "columnDefs": [
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            return row.name
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, row, meta) {
                            if (row.role == 1) {
                                return '<span class="badge bg-success">Admin</span>';
                            } else {
                                return '<span class="badge bg-warning">Customer</span>';
                            }
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, row, meta) {
                            let dateTime = '';
                            if (type === 'display') {
                                dateTime = moment(row.created_at).format('DD-MMM-YYYY');
                            }
                            return dateTime;
                        }
                    },
                ]
            });
        });

        $(function () {
            showSwal = function (type, url) {
                'use strict';
                if (type === 'passing-parameter-execute-cancel') {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger me-2'
                        },
                        buttonsStyling: false,
                    })

                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'me-2',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {

                            window.location.href = url;
                            // swalWithBootstrapButtons.fire(
                            //     'Deleted!',
                            //     'Your file has been deleted.',
                            //     'success'
                            // ).then(() => {
                            //     window.location.href = url;
                            // });

                        } else if (
                            result.dismiss === Swal.DismissReason.cancel
                        ) {
                            swalWithBootstrapButtons.fire(
                                'Cancelled',
                                'Your imaginary file is safe :)',
                                'error'
                            )
                        }
                    })
                }
            }
        });

        $(document).ready(function () {

            $('#createUserModal').on('shown.bs.modal', function () {
                $('.select2-no-search').select2({
                    minimumResultsForSearch: Infinity,
                    width: '100%',
                    dropdownParent: $('#createUserModal')
                });
            });

            $('#editUserRole').select2({
                minimumResultsForSearch: Infinity,
                width: '100%',
                dropdownParent: $('#editBlogModal')
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createUserForm');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('userPassword');
            const passwordIcon = document.getElementById('passwordToggleIcon');

            if (togglePasswordBtn) {
                togglePasswordBtn.addEventListener('click', function() {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;
                    passwordIcon.classList.toggle('fa-eye');
                    passwordIcon.classList.toggle('fa-eye-slash');
                });
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                clearValidation();

                let isValid = true;

                const name = document.getElementById('userName');
                if (!name.value.trim()) {
                    showError(name, 'Name is required');
                    isValid = false;
                } else if (name.value.trim().length < 2) {
                    showError(name, 'Name must be at least 2 characters');
                    isValid = false;
                }

                // Email validation
                const email = document.getElementById('userEmail');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email.value.trim()) {
                    showError(email, 'Email is required');
                    isValid = false;
                } else if (!emailPattern.test(email.value)) {
                    showError(email, 'Please enter a valid email address');
                    isValid = false;
                }

                // Role validation
                const role = document.getElementById('userRole');
                if (!role.value) {
                    showError(role, 'Please select a role');
                    isValid = false;
                }

                // Password validation
                const password = document.getElementById('userPassword');
                if (!password.value) {
                    showError(password, 'Password is required');
                    isValid = false;
                } else if (password.value.length < 6) {
                    showError(password, 'Password must be at least 6 characters');
                    isValid = false;
                }

                if (isValid) {
                    form.submit();
                }
            });

            function showError(input, message) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback') ||
                    input.closest('.mb-3').querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = message;
                    feedback.style.display = 'block';
                }
            }

            function clearValidation() {
                const inputs = form.querySelectorAll('.form-control, .form-select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });

                const feedbacks = form.querySelectorAll('.invalid-feedback');
                feedbacks.forEach(feedback => {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                });
            }

            const inputs = form.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentElement.querySelector('.invalid-feedback') ||
                            this.closest('.mb-3').querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = '';
                            feedback.style.display = 'none';
                        }
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('editUserForm');

            document.addEventListener('click', function(e) {
                const button = e.target.closest('a[data-bs-target="#editBlogModal"]');
                if (!button) return;

                const name = button.getAttribute('data-name');
                const email = button.getAttribute('data-email');
                const role = button.getAttribute('data-role');
                const address = button.getAttribute('data-address');
                const url = button.getAttribute('data-url');

                editForm.querySelector('#editUserName').value = name;
                editForm.querySelector('#editUserEmail').value = email;
                editForm.querySelector('#editUserRole').value = role;
                $('#editUserRole').trigger('change');
                editForm.querySelector('#editUserAddress').value = address || '';

                editForm.setAttribute('action', url);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('editUserForm');
            const togglePasswordBtn = document.getElementById('editTogglePassword');
            const passwordInput = document.getElementById('editUserPassword');
            const passwordIcon = document.getElementById('editPasswordToggleIcon');

            if (togglePasswordBtn) {
                togglePasswordBtn.addEventListener('click', () => {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;
                    passwordIcon.classList.toggle('fa-eye');
                    passwordIcon.classList.toggle('fa-eye-slash');
                });
            }

            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                clearValidation(editForm);

                let isValid = true;

                const name = document.getElementById('editUserName');
                if (!name.value.trim()) {
                    showError(name, 'Name is required');
                    isValid = false;
                } else if (name.value.trim().length < 2) {
                    showError(name, 'Name must be at least 2 characters');
                    isValid = false;
                }

                const email = document.getElementById('editUserEmail');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email.value.trim()) {
                    showError(email, 'Email is required');
                    isValid = false;
                } else if (!emailPattern.test(email.value)) {
                    showError(email, 'Please enter a valid email address');
                    isValid = false;
                }

                const role = document.getElementById('editUserRole');
                if (!role.value) {
                    showError(role, 'Please select a role');
                    isValid = false;
                }

                if (isValid) editForm.submit();
            });

            function showError(input, message) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback') ||
                    input.closest('.mb-3').querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = message;
                    feedback.style.display = 'block';
                }
            }

            function clearValidation(form) {
                const inputs = form.querySelectorAll('.form-control, .form-select');
                inputs.forEach(input => input.classList.remove('is-invalid'));

                const feedbacks = form.querySelectorAll('.invalid-feedback');
                feedbacks.forEach(fb => {
                    fb.textContent = '';
                    fb.style.display = 'none';
                });
            }

            const inputs = editForm.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        input.classList.remove('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback') ||
                            input.closest('.mb-3').querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = '';
                    }
                });
            });
        });

    </script>
@endpush
