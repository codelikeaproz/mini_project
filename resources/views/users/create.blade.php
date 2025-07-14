@extends('layouts.app')

@section('title', 'Add New MDRRMO Staff')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Add New MDRRMO Staff</h1>
                <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Staff List
                </a>
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
                    <h6 class="m-0 font-weight-bold text-primary">New Staff Member Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">
                                    <strong>First Name <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name"
                                       value="{{ old('first_name') }}" required>
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
                                       value="{{ old('last_name') }}" required>
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
                                       value="{{ old('email') }}" required>
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
                                       value="{{ old('phone_number') }}"
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
                                    <option value="Maramag" {{ old('municipality') == 'Maramag' ? 'selected' : '' }}>Maramag</option>
                                    <option value="Valencia" {{ old('municipality') == 'Valencia' ? 'selected' : '' }}>Valencia</option>
                                    <option value="Malaybalay" {{ old('municipality') == 'Malaybalay' ? 'selected' : '' }}>Malaybalay</option>
                                    <option value="Don Carlos" {{ old('municipality') == 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                    <option value="Quezon" {{ old('municipality') == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                    <option value="San Fernando" {{ old('municipality') == 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                                    <option value="Kitaotao" {{ old('municipality') == 'Kitaotao' ? 'selected' : '' }}>Kitaotao</option>
                                    <option value="Lantapan" {{ old('municipality') == 'Lantapan' ? 'selected' : '' }}>Lantapan</option>
                                    <option value="Manolo Fortich" {{ old('municipality') == 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
                                    <option value="Libona" {{ old('municipality') == 'Libona' ? 'selected' : '' }}>Libona</option>
                                    <option value="Sumilao" {{ old('municipality') == 'Sumilao' ? 'selected' : '' }}>Sumilao</option>
                                    <option value="Impasugong" {{ old('municipality') == 'Impasugong' ? 'selected' : '' }}>Impasugong</option>
                                    <option value="Baungon" {{ old('municipality') == 'Baungon' ? 'selected' : '' }}>Baungon</option>
                                    <option value="Talakag" {{ old('municipality') == 'Talakag' ? 'selected' : '' }}>Talakag</option>
                                    <option value="Cabanglasan" {{ old('municipality') == 'Cabanglasan' ? 'selected' : '' }}>Cabanglasan</option>
                                    <option value="Damulog" {{ old('municipality') == 'Damulog' ? 'selected' : '' }}>Damulog</option>
                                    <option value="Dangcagan" {{ old('municipality') == 'Dangcagan' ? 'selected' : '' }}>Dangcagan</option>
                                    <option value="Kadingilan" {{ old('municipality') == 'Kadingilan' ? 'selected' : '' }}>Kadingilan</option>
                                    <option value="Kalilangan" {{ old('municipality') == 'Kalilangan' ? 'selected' : '' }}>Kalilangan</option>
                                    <option value="Kibawe" {{ old('municipality') == 'Kibawe' ? 'selected' : '' }}>Kibawe</option>
                                    <option value="Pangantucan" {{ old('municipality') == 'Pangantucan' ? 'selected' : '' }}>Pangantucan</option>
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
                                       value="{{ old('position') }}"
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
                            <div class="col-md-12 mb-3">
                                <label for="role" class="form-label">
                                    <strong>System Role <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('role') is-invalid @enderror"
                                        id="role" name="role" required>
                                    <option value="">Select Role...</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                                    <option value="mdrrmo_staff" {{ old('role') == 'mdrrmo_staff' ? 'selected' : '' }}>MDRRMO Staff (Standard Access)</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <strong>Administrator:</strong> Full system access including user management<br>
                                    <strong>MDRRMO Staff:</strong> Can manage incidents and vehicles but not users
                                </small>
                            </div>
                        </div>

                        <!-- Password -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Login Credentials</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <strong>Password <span class="text-danger">*</span></strong>
                                </label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <strong>Confirm Password <span class="text-danger">*</span></strong>
                                </label>
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                                <small class="form-text text-muted">Must match the password above</small>
                            </div>
                        </div>

                        <!-- Important Notice -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Important Notice</h6>
                            <ul class="mb-0">
                                <li>A verification email will be sent to the provided email address</li>
                                <li>The user must verify their email before accessing the system</li>
                                <li>The account will be active immediately but requires email verification</li>
                                <li>You can resend verification emails from the user management page</li>
                            </ul>
                        </div>

                        <!-- Form Actions -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Create MDRRMO Staff Account
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
    // Auto-focus first field
    $('#first_name').focus();

    // Password strength indicator (basic)
    $('#password').on('input', function() {
        const password = $(this).val();
        const strength = calculatePasswordStrength(password);

        // Add visual feedback for password strength
        const strengthBar = $('#password-strength');
        if (strengthBar.length === 0) {
            $(this).after('<div id="password-strength" class="mt-1"></div>');
        }

        let strengthText = '';
        let strengthClass = '';

        if (password.length === 0) {
            strengthText = '';
        } else if (strength < 2) {
            strengthText = '<small class="text-danger">Weak password</small>';
        } else if (strength < 4) {
            strengthText = '<small class="text-warning">Medium password</small>';
        } else {
            strengthText = '<small class="text-success">Strong password</small>';
        }

        $('#password-strength').html(strengthText);
    });

    // Confirm password validation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();

        if (confirmation && password !== confirmation) {
            $(this).addClass('is-invalid');
            if ($('#password-match').length === 0) {
                $(this).after('<div id="password-match" class="invalid-feedback">Passwords do not match</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $('#password-match').remove();
        }
    });
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
