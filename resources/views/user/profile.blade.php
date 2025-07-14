@extends('layouts.app')

@section('title', 'My Profile - MDRRMO Staff')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
            <p class="mb-0 text-gray-600">Manage your personal information and account settings</p>
        </div>
        <div class="d-sm-flex align-items-center">
            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">
                                    <strong>First Name <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name"
                                       name="first_name"
                                       value="{{ old('first_name', $user->first_name) }}"
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">
                                    <strong>Last Name <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name"
                                       name="last_name"
                                       value="{{ old('last_name', $user->last_name) }}"
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <strong>Email Address <span class="text-danger">*</span></strong>
                                </label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Used for system notifications and login</small>
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <strong>Phone Number</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number"
                                       name="phone_number"
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="e.g., +63912-345-6789">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Position -->
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">
                                    <strong>Position/Title</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('position') is-invalid @enderror"
                                       id="position"
                                       name="position"
                                       value="{{ old('position', $user->position) }}"
                                       placeholder="e.g., Emergency Response Officer">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Municipality -->
                            <div class="col-md-6 mb-3">
                                <label for="municipality" class="form-label">
                                    <strong>Municipality</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('municipality') is-invalid @enderror"
                                       id="municipality"
                                       name="municipality"
                                       value="{{ old('municipality', $user->municipality) }}"
                                       readonly>
                                @error('municipality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Contact administrator to change municipality</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Information Sidebar -->
        <div class="col-lg-4">
            <!-- Account Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Role:</strong>
                        <span class="badge badge-primary">MDRRMO Staff</span>
                    </div>
                    <div class="mb-3">
                        <strong>Account Status:</strong>
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Email Verified:</strong>
                        @if($user->is_verified)
                            <span class="badge badge-success">Verified</span>
                        @else
                            <span class="badge badge-warning">Not Verified</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Member Since:</strong>
                        <br><small class="text-muted">{{ $user->created_at->format('F d, Y') }}</small>
                    </div>
                    <div class="mb-3">
                        <strong>Last Login:</strong>
                        <br><small class="text-muted">
                            {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Security</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-shield-alt"></i>
                        Change your password to keep your account secure.
                    </p>

                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="change_password" value="1">

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <strong>Current Password</strong>
                            </label>
                            <input type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password"
                                   name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <strong>New Password</strong>
                            </label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <strong>Confirm New Password</strong>
                            </label>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   name="password_confirmation">
                        </div>

                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Emergency Contacts -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Emergency Contacts</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>MDRRMO Maramag:</strong>
                        <br><a href="tel:+639123456789" class="text-primary">0912-345-6789</a>
                    </div>
                    <div class="mb-2">
                        <strong>System Administrator:</strong>
                        <br><small class="text-muted">Contact for technical issues</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
