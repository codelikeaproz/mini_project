@extends('layouts.app')

@section('title', 'User Details - ' . $user->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">MDRRMO Staff Details</h1>
                    <p class="mb-0 text-gray-600">{{ $user->full_name }} - {{ $user->municipality }}</p>
                </div>
                <div class="d-sm-flex">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning shadow-sm mr-2">
                        <i class="fas fa-edit fa-sm text-white-50"></i> Edit User
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <!-- Personal Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="icon-circle bg-primary mx-auto mb-3" style="width: 5rem; height: 5rem;">
                                    <i class="fas fa-user text-white fa-2x"></i>
                                </div>
                                <h4 class="mb-1">{{ $user->full_name }}</h4>
                                <p class="text-muted">{{ $user->position ?: 'MDRRMO Staff' }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>First Name:</strong></div>
                                <div class="col-sm-8">{{ $user->first_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Last Name:</strong></div>
                                <div class="col-sm-8">{{ $user->last_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">
                                    <a href="mailto:{{ $user->email }}" class="text-primary">{{ $user->email }}</a>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Phone:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->phone_number)
                                        <a href="tel:{{ $user->phone_number }}" class="text-primary">{{ $user->phone_number }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Municipality:</strong></div>
                                <div class="col-sm-8">{{ $user->municipality }}</div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><strong>Position:</strong></div>
                                <div class="col-sm-8">{{ $user->position ?: 'Not specified' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Role:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->role === 'admin')
                                        <span class="badge badge-danger">Administrator</span>
                                        <br><small class="text-muted">Full system access</small>
                                    @else
                                        <span class="badge badge-primary">MDRRMO Staff</span>
                                        <br><small class="text-muted">Standard user access</small>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Email Status:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->is_verified)
                                        <span class="badge badge-success">Verified</span>
                                        <br><small class="text-muted">Email verified on {{ $user->email_verified_at?->format('M d, Y') }}</small>
                                    @else
                                        <span class="badge badge-warning">Unverified</span>
                                        <br><small class="text-muted">Verification email sent</small>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Joined:</strong></div>
                                <div class="col-sm-8">
                                    {{ $user->created_at->format('F d, Y \a\t g:i A') }}
                                    <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->updated_at && $user->updated_at != $user->created_at)
                                        {{ $user->updated_at->format('F d, Y \a\t g:i A') }}
                                        <br><small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never updated</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><strong>Last Login:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('F d, Y \a\t g:i A') }}
                                        <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never logged in</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                        </div>
                        <div class="card-body">
                            @if($recentActivities->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Description</th>
                                                <th>Date</th>
                                                <th>IP Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentActivities as $activity)
                                                <tr>
                                                    <td>
                                                        @switch($activity->action)
                                                            @case('login')
                                                                <span class="badge badge-success">Login</span>
                                                                @break
                                                            @case('logout')
                                                                <span class="badge badge-secondary">Logout</span>
                                                                @break
                                                            @case('incident_created')
                                                                <span class="badge badge-primary">Incident Created</span>
                                                                @break
                                                            @case('incident_updated')
                                                                <span class="badge badge-info">Incident Updated</span>
                                                                @break
                                                            @case('vehicle_updated')
                                                                <span class="badge badge-warning">Vehicle Updated</span>
                                                                @break
                                                            @default
                                                                <span class="badge badge-light">{{ ucfirst($activity->action) }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>{{ $activity->description }}</td>
                                                    <td>
                                                        {{ $activity->created_at->format('M d, Y H:i') }}
                                                        <br><small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                    </td>
                                                    <td><small class="text-muted">{{ $activity->ip_address }}</small></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No recent activity recorded for this user.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Administrative Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit User Information
                                    </a>
                                    @if(!$user->is_verified)
                                        <button type="button" class="btn btn-info ml-2" onclick="resendVerification()">
                                            <i class="fas fa-envelope"></i> Resend Verification Email
                                        </button>
                                    @endif
                                    @if(!$user->is_active)
                                        <button type="button" class="btn btn-success ml-2" onclick="activateUser()">
                                            <i class="fas fa-user-check"></i> Activate Account
                                        </button>
                                    @endif
                                </div>
                                <div>
                                    @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                            <i class="fas fa-trash"></i> Delete User
                                        </button>
                                    @else
                                        <span class="text-muted"><i class="fas fa-info-circle"></i> Cannot delete your own account</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete functionality now handled by SweetAlert2 -->
@endsection

@push('styles')
<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    showDeleteConfirmation(
        'Delete User',
        'Are you sure you want to delete this MDRRMO staff member?',
        '{{ $user->full_name }}',
        'Yes, Delete User',
        function() {
            showLoading('Deleting user...');

            fetch(`/users/{{ $user->id }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                closeLoading();
                if (data.success) {
                    showSuccessToast(data.message || 'User deleted successfully');
                    setTimeout(() => {
                        window.location.href = '/users';
                    }, 1500);
                } else {
                    showErrorToast(data.message || 'Delete failed');
                }
            })
            .catch(error => {
                closeLoading();
                console.error('Delete error:', error);
                showErrorToast('Delete failed: ' + error.message);
            });
        }
    );
}

function resendVerification() {
    showDeleteConfirmation(
        'Resend Verification Email',
        'Are you sure you want to resend the verification email to this user?',
        '{{ $user->email }}',
        'Yes, Resend Email',
        function() {
            showLoading('Sending verification email...');

            fetch(`/users/{{ $user->id }}/resend-verification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                closeLoading();
                if (data.success) {
                    showSuccessToast(data.message || 'Verification email sent successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showErrorToast(data.message || 'Failed to send verification email');
                }
            })
            .catch(error => {
                closeLoading();
                console.error('Resend verification error:', error);
                showErrorToast('Failed to send verification email: ' + error.message);
            });
        }
    );
}

function activateUser() {
    // Implementation for activating user
    if (confirm('Are you sure you want to activate this user account?')) {
        // Add AJAX call to activate user
        alert('User account activated successfully');
    }
}
</script>
@endpush
