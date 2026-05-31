@extends('layouts.app')

@section('title', 'User Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Profile Card -->
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $user->getProfilePictureUrl() }}?v={{ $user->updated_at->timestamp }}"
                         alt="Profile" class="profile-picture-preview">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    @if ($user->gender)
                        <p class="text-muted mb-1" style="font-size: 0.85rem;">
                            <i class="fas fa-venus-mars"></i> {{ ucfirst($user->gender) }}
                        </p>
                    @endif
                    @if ($user->address)
                        <p class="text-muted mb-1" style="font-size: 0.85rem;">
                            <i class="fas fa-location-dot"></i> {{ $user->address }}
                        </p>
                    @endif
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted d-block">
                            <i class="fas fa-calendar-alt"></i> Member since {{ $user->created_at->format('M d, Y') }}
                        </small>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-clock"></i> Last updated {{ $user->updated_at->diffForHumans() }}
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
                    <form id="profileForm" enctype="multipart/form-data">
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

@endsection

@section('extra-js')
    <script>
        const profileUpdateUrl = @json(route('profile.update'));
        const csrfToken = @json(csrf_token());

        function clearProfileErrors() {
            ['name', 'email', 'gender', 'address', 'profilePicture'].forEach(k => {
                const el = document.getElementById(k + 'Error');
                if (el) el.textContent = '';
            });
        }

        function submitProfile() {
            clearProfileErrors();

            const form = document.getElementById('profileForm');
            const formData = new FormData(form);

            // Force the spoofed method + token to be present regardless of hidden inputs
            formData.set('_method', 'PUT');
            formData.set('_token', csrfToken);

            const saveBtn = document.querySelector('button[onclick="submitProfile()"]');
            if (saveBtn) { saveBtn.disabled = true; saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...'; }

            $.ajax({
                url: profileUpdateUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                success(response) {
                    toastr.success(response.message || 'Profile updated!');
                    setTimeout(() => location.reload(), 800);
                },
                error(xhr) {
                    if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes'; }

                    const data = xhr.responseJSON || {};
                    const errors = data.errors || {};
                    const map = {
                        name: 'nameError',
                        email: 'emailError',
                        gender: 'genderError',
                        address: 'addressError',
                        profile_picture: 'profilePictureError',
                    };
                    let hadFieldError = false;
                    Object.keys(errors).forEach(field => {
                        const id = map[field];
                        if (id) {
                            const el = document.getElementById(id);
                            if (el) el.textContent = errors[field][0];
                            hadFieldError = true;
                        }
                    });

                    if (hadFieldError) {
                        toastr.error('Please fix the highlighted fields.');
                    } else if (xhr.status === 419) {
                        toastr.error('Session expired. Refresh the page and try again.');
                    } else if (xhr.status === 0) {
                        toastr.error('Network error — server unreachable.');
                    } else {
                        toastr.error(data.message || ('Save failed (HTTP ' + xhr.status + ')'));
                    }
                    console.error('Profile save failed:', xhr.status, xhr.responseText);
                },
                complete() {
                    if (saveBtn && saveBtn.disabled) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                    }
                }
            });
        }

        document.getElementById('profile_picture').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.querySelector('.profile-picture-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
