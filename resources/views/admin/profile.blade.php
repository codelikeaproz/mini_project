@extends('layouts.app')

@section('title', 'Admin Profile - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Administrator Profile</h1>
            <p class="page-subtitle">
                Manage your administrator account settings
                <span class="badge bg-danger">Administrator</span>
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Administrator Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Personal Information</h6>

                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name"
                                           value="{{ old('first_name', $user->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name"
                                           value="{{ old('last_name', $user->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                           id="position" name="position"
                                           value="{{ old('position', $user->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                           id="phone_number" name="phone_number"
                                           value="{{ old('phone_number', $user->phone_number) }}"
                                           placeholder="+63XXX-XXX-XXXX">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Account Information</h6>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email"
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="municipality" class="form-label">Municipality</label>
                                    <input type="text" class="form-control"
                                           value="{{ $user->municipality }}" disabled>
                                    <div class="form-text">Municipality cannot be changed</div>
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control"
                                           value="Administrator" disabled>
                                    <div class="form-text">Role cannot be changed</div>
                                </div>

                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                           id="avatar" name="avatar" accept="image/*">
                                    <div class="form-text">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</div>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Change Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Change Password</h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Leave password fields empty if you don't want to change your password.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" minlength="8">
                                    <div class="form-text">Minimum 8 characters</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" minlength="8">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Details Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Details</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($user->avatar ?? false)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile Picture"
                                 class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                            </div>
                        @endif
                        <h6 class="mb-0">{{ $user->full_name }}</h6>
                        <small class="text-muted">{{ $user->position }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Status</label>
                        <div>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Verification</label>
                        <div>
                            @if($user->is_verified)
                                <span class="badge bg-success">Verified</span>
                                <br><small class="text-muted">{{ $user->email_verified_at?->format('M d, Y') }}</small>
                            @else
                                <span class="badge bg-warning">Unverified</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Login</label>
                        <div>
                            @if($user->last_login_at)
                                <small>{{ $user->last_login_at->format('M d, Y g:i A') }}</small>
                            @else
                                <small class="text-muted">Never logged in</small>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Created</label>
                        <div>
                            <small>{{ $user->created_at->format('M d, Y g:i A') }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Updated</label>
                        <div>
                            <small>{{ $user->updated_at->format('M d, Y g:i A') }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.login-attempts') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-shield-alt me-2"></i>View Security Logs
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-users me-2"></i>Manage Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);

            // You could add a password strength indicator here
        });
    }

    // Confirm password match validation
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (password !== confirmPassword && confirmPassword.length > 0) {
                this.setCustomValidity('Passwords do not match');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    }
});
</script>
@endpush
