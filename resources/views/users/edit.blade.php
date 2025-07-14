@extends('layouts.app')

@section('title', 'Edit User - ' . $user->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit MDRRMO Staff</h1>
                <div class="d-sm-flex">
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info shadow-sm mr-2">
                        <i class="fas fa-eye fa-sm text-white-50"></i> View Details
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Staff Information - {{ $user->full_name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">
                                    <strong>First Name <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name"
                                       value="{{ old('first_name', $user->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">
                                    <strong>Last Name <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name"
                                       value="{{ old('last_name', $user->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <strong>Email Address <span class="text-danger">*</span></strong>
                                </label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Used for login and system notifications</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <strong>Phone Number</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number" name="phone_number"
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="e.g., 09123456789">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Position and Location -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Position & Location</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="municipality" class="form-label">
                                    <strong>Municipality <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('municipality') is-invalid @enderror"
                                        id="municipality" name="municipality" required>
                                    <option value="">Select Municipality...</option>
                                    <option value="Maramag" {{ old('municipality', $user->municipality) == 'Maramag' ? 'selected' : '' }}>Maramag</option>
                                    <option value="Valencia" {{ old('municipality', $user->municipality) == 'Valencia' ? 'selected' : '' }}>Valencia</option>
                                    <option value="Malaybalay" {{ old('municipality', $user->municipality) == 'Malaybalay' ? 'selected' : '' }}>Malaybalay</option>
                                    <option value="Don Carlos" {{ old('municipality', $user->municipality) == 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                    <option value="Quezon" {{ old('municipality', $user->municipality) == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                    <option value="San Fernando" {{ old('municipality', $user->municipality) == 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                                    <option value="Kitaotao" {{ old('municipality', $user->municipality) == 'Kitaotao' ? 'selected' : '' }}>Kitaotao</option>
                                    <option value="Lantapan" {{ old('municipality', $user->municipality) == 'Lantapan' ? 'selected' : '' }}>Lantapan</option>
                                    <option value="Manolo Fortich" {{ old('municipality', $user->municipality) == 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
                                    <option value="Libona" {{ old('municipality', $user->municipality) == 'Libona' ? 'selected' : '' }}>Libona</option>
                                    <option value="Sumilao" {{ old('municipality', $user->municipality) == 'Sumilao' ? 'selected' : '' }}>Sumilao</option>
                                    <option value="Impasugong" {{ old('municipality', $user->municipality) == 'Impasugong' ? 'selected' : '' }}>Impasugong</option>
                                    <option value="Baungon" {{ old('municipality', $user->municipality) == 'Baungon' ? 'selected' : '' }}>Baungon</option>
                                    <option value="Talakag" {{ old('municipality', $user->municipality) == 'Talakag' ? 'selected' : '' }}>Talakag</option>
                                    <option value="Cabanglasan" {{ old('municipality', $user->municipality) == 'Cabanglasan' ? 'selected' : '' }}>Cabanglasan</option>
                                    <option value="Damulog" {{ old('municipality', $user->municipality) == 'Damulog' ? 'selected' : '' }}>Damulog</option>
                                    <option value="Dangcagan" {{ old('municipality', $user->municipality) == 'Dangcagan' ? 'selected' : '' }}>Dangcagan</option>
                                    <option value="Kadingilan" {{ old('municipality', $user->municipality) == 'Kadingilan' ? 'selected' : '' }}>Kadingilan</option>
                                    <option value="Kalilangan" {{ old('municipality', $user->municipality) == 'Kalilangan' ? 'selected' : '' }}>Kalilangan</option>
                                    <option value="Kibawe" {{ old('municipality', $user->municipality) == 'Kibawe' ? 'selected' : '' }}>Kibawe</option>
                                    <option value="Pangantucan" {{ old('municipality', $user->municipality) == 'Pangantucan' ? 'selected' : '' }}>Pangantucan</option>
                                </select>
                                @error('municipality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">
                                    <strong>Position/Title</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('position') is-invalid @enderror"
                                       id="position" name="position"
                                       value="{{ old('position', $user->position) }}"
                                       placeholder="e.g., MDRRMO Officer, Emergency Responder">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- System Access -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">System Access</h5>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="role" class="form-label">
                                    <strong>System Role <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('role') is-invalid @enderror"
                                        id="role" name="role" required>
                                    <option value="">Select Role...</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                                    <option value="mdrrmo_staff" {{ old('role', $user->role) == 'mdrrmo_staff' ? 'selected' : '' }}>MDRRMO Staff (Standard Access)</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <strong>Administrator:</strong> Full system access including user management<br>
                                    <strong>MDRRMO Staff:</strong> Can manage incidents and vehicles but not users
                                </small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="is_active" class="form-label">
                                    <strong>Account Status</strong>
                                </label>
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="is_active" name="is_active"
                                           value="1"
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                    </label>
                                </div>
                                <small class="form-text text-muted">Unchecking will disable login for this user</small>
                            </div>
                        </div>

                        <!-- Password Update -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Password Update <small class="text-muted">(Optional)</small></h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <strong>New Password</strong>
                                </label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <strong>Confirm New Password</strong>
                                </label>
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation" name="password_confirmation">
                                <small class="form-text text-muted">Required only if changing password</small>
                            </div>
                        </div>

                        <!-- Account Information Display -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Account Information</h5>

                        <div class="alert alert-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Current Status:</strong>
                                    <ul class="mb-0">
                                        <li>Active: {{ $user->is_active ? 'Yes' : 'No' }}</li>
                                        <li>Email Verified: {{ $user->is_verified ? 'Yes' : 'No' }}</li>
                                        <li>Account Created: {{ $user->created_at->format('F d, Y') }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>Login Information:</strong>
                                    <ul class="mb-0">
                                        <li>Last Login: {{ $user->last_login_at ? $user->last_login_at->format('F d, Y \a\t g:i A') : 'Never' }}</li>
                                        <li>Last Updated: {{ $user->updated_at != $user->created_at ? $user->updated_at->format('F d, Y') : 'Never' }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if($user->id === auth()->id())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> You are editing your own account. Be careful when changing your role or account status to avoid losing access.
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Staff Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Password strength indicator
    $('#password').on('input', function() {
        const password = $(this).val();

        if (password.length > 0) {
            const strength = calculatePasswordStrength(password);

            // Add/update strength indicator
            if ($('#password-strength').length === 0) {
                $(this).after('<div id="password-strength" class="mt-1"></div>');
            }

            let strengthText = '';
            if (strength < 2) {
                strengthText = '<small class="text-danger">Weak password</small>';
            } else if (strength < 4) {
                strengthText = '<small class="text-warning">Medium password</small>';
            } else {
                strengthText = '<small class="text-success">Strong password</small>';
            }

            $('#password-strength').html(strengthText);
        } else {
            $('#password-strength').remove();
        }
    });

    // Confirm password validation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();

        if (password.length > 0 && confirmation.length > 0) {
            if (password !== confirmation) {
                $(this).addClass('is-invalid');
                if ($('#password-match').length === 0) {
                    $(this).after('<div id="password-match" class="invalid-feedback">Passwords do not match</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $('#password-match').remove();
            }
        } else {
            $(this).removeClass('is-invalid');
            $('#password-match').remove();
        }
    });

    // Role change warning for self
    @if($user->id === auth()->id())
    $('#role').on('change', function() {
        const selectedRole = $(this).val();
        if (selectedRole === 'mdrrmo_staff') {
            if (!confirm('Warning: Changing your role to "MDRRMO Staff" will remove your admin privileges. Are you sure?')) {
                $(this).val('admin');
            }
        }
    });

    $('#is_active').on('change', function() {
        if (!$(this).prop('checked')) {
            if (!confirm('Warning: Deactivating your own account will log you out and prevent future logins. Are you sure?')) {
                $(this).prop('checked', true);
            }
        }
    });
    @endif
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    return strength;
}
</script>
@endpush
