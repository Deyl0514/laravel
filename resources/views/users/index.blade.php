@extends('layouts.app')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email...">
                <button class="btn btn-primary" onclick="filterUsers()">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users text-primary"></i> All Users
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $user->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                            onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-inbox" style="font-size: 2rem; color: #cbd5e1;"></i>
                                    <p class="text-muted mt-3">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <nav aria-label="Page navigation" class="mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </nav>
            @endif
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUserForm">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="addName" required>
                            <small class="text-danger" id="addNameError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="addEmail" required>
                            <small class="text-danger" id="addEmailError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="addPassword" required>
                            <small class="text-danger" id="addPasswordError"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitAddUser()">
                            <i class="fas fa-save"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit"></i> Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editName" required>
                            <small class="text-danger" id="editNameError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" required>
                            <small class="text-danger" id="editEmailError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (Leave blank to keep current)</label>
                            <input type="password" class="form-control" id="editPassword">
                            <small class="text-danger" id="editPasswordError"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitEditUser()">
                            <i class="fas fa-save"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        function editUser(id, name, email) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
        }

        function submitAddUser() {
            const name = document.getElementById('addName').value;
            const email = document.getElementById('addEmail').value;
            const password = document.getElementById('addPassword').value;

            $.ajax({
                url: '{{ route("users.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    email: email,
                    password: password
                },
                success: function(response) {
                    toastr.success(response.message);
                    document.getElementById('addUserForm').reset();
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.name) {
                            document.getElementById('addNameError').textContent = xhr.responseJSON.errors.name[0];
                        }
                        if (xhr.responseJSON.errors.email) {
                            document.getElementById('addEmailError').textContent = xhr.responseJSON.errors.email[0];
                        }
                        if (xhr.responseJSON.errors.password) {
                            document.getElementById('addPasswordError').textContent = xhr.responseJSON.errors.password[0];
                        }
                    }
                }
            });
        }

        function submitEditUser() {
            const id = document.getElementById('editUserId').value;
            const name = document.getElementById('editName').value;
            const email = document.getElementById('editEmail').value;
            const password = document.getElementById('editPassword').value;

            $.ajax({
                url: `/users/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    email: email,
                    password: password || null
                },
                success: function(response) {
                    toastr.success(response.message);
                    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.name) {
                            document.getElementById('editNameError').textContent = xhr.responseJSON.errors.name[0];
                        }
                        if (xhr.responseJSON.errors.email) {
                            document.getElementById('editEmailError').textContent = xhr.responseJSON.errors.email[0];
                        }
                        if (xhr.responseJSON.errors.password) {
                            document.getElementById('editPasswordError').textContent = xhr.responseJSON.errors.password[0];
                        }
                    }
                }
            });
        }

        function deleteUser(id, name) {
            if (confirm(`Are you sure you want to delete ${name}?`)) {
                $.ajax({
                    url: `/users/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function() {
                        toastr.error('Error deleting user');
                    }
                });
            }
        }

        function filterUsers() {
            const search = document.getElementById('searchInput').value;
            window.location.href = '{{ route("users.index") }}?search=' + encodeURIComponent(search);
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterUsers();
            }
        });
    </script>
@endsection
