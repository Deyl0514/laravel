@extends('layouts.app')

@section('title', 'User Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Profile Card -->
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $user->getProfilePictureUrl() }}" alt="Profile" class="profile-picture-preview">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i> Member since {{ $user->created_at->format('M d, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Edit Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit text-primary"></i> Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ $user->name }}" required>
                                <small class="text-danger" id="nameError"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ $user->email }}" required>
                                <small class="text-danger" id="emailError"></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $user->gender === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <small class="text-danger" id="genderError"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <small class="text-muted">Max 2MB. Formats: JPG, PNG, GIF</small>
                                <small class="text-danger" id="profilePictureError"></small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Enter your address">{{ $user->address }}</textarea>
                            <small class="text-danger" id="addressError"></small>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            Fill in the fields you want to update and click the Save button below.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="submitProfile()">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card tasks">
                <div class="stat-icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="stat-number">{{ auth()->user()->tasks()->count() }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="stat-card completed">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number">{{ auth()->user()->tasks()->where('status', 'completed')->count() }}</div>
                <div class="stat-label">Completed Tasks</div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-number">{{ auth()->user()->tasks()->where('status', '!=', 'completed')->count() }}</div>
                <div class="stat-label">Active Tasks</div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        function submitProfile() {
            const formData = new FormData(document.getElementById('profileForm'));

            $.ajax({
                url: '{{ route("profile.update") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON.errors) {
                        // Clear previous errors
                        document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

                        // Display new errors
                        if (xhr.responseJSON.errors.name) {
                            document.getElementById('nameError').textContent = xhr.responseJSON.errors.name[0];
                        }
                        if (xhr.responseJSON.errors.email) {
                            document.getElementById('emailError').textContent = xhr.responseJSON.errors.email[0];
                        }
                        if (xhr.responseJSON.errors.gender) {
                            document.getElementById('genderError').textContent = xhr.responseJSON.errors.gender[0];
                        }
                        if (xhr.responseJSON.errors.address) {
                            document.getElementById('addressError').textContent = xhr.responseJSON.errors.address[0];
                        }
                        if (xhr.responseJSON.errors.profile_picture) {
                            document.getElementById('profilePictureError').textContent = xhr.responseJSON.errors.profile_picture[0];
                        }
                    }
                }
            });
        }

        // Preview image before upload
        document.getElementById('profile_picture').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
