@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">MDRRMO User Management</h1>
                <div class="d-sm-flex">
                    <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-user-plus fa-sm text-white-50"></i> Add New Staff
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Search and Filter Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Search MDRRMO Staff</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search by name, email, municipality, or position..."
                                       value="{{ $search }}">
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users DataTable Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">MDRRMO Staff Directory</h6>
                    <span class="badge badge-primary">{{ $users->total() }} Total Users</span>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Staff Information</th>
                                        <th>Role</th>
                                        <th>Municipality</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-primary">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->full_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                        @if($user->position)
                                                            <br><small class="text-info">{{ $user->position }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($user->role === 'admin')
                                                    <span class="badge badge-danger">Administrator</span>
                                                @else
                                                    <span class="badge badge-primary">MDRRMO Staff</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->municipality }}</td>
                                            <td>
                                                @if($user->phone_number)
                                                    <a href="tel:{{ $user->phone_number }}" class="text-primary">
                                                        {{ $user->phone_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    @if($user->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </div>
                                                <div class="mt-1">
                                                    @if($user->is_verified)
                                                        <span class="badge badge-info">Verified</span>
                                                    @else
                                                        <span class="badge badge-warning">Unverified</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{ $user->created_at->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                       class="btn btn-sm btn-warning" title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$user->is_verified)
                                                        <button type="button" class="btn btn-sm btn-info"
                                                                onclick="resendVerification({{ $user->id }}, '{{ $user->email }}')"
                                                                title="Resend Verification Email">
                                                            <i class="fas fa-envelope"></i>
                                                        </button>
                                                    @endif
                                                    @if($user->id !== auth()->id())
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->full_name }}')"
                                                                title="Delete User">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $users->appends(['search' => $search])->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            @if($search)
                                <p class="text-gray-500">No users found matching "{{ $search }}".</p>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Show All Users
                                </a>
                            @else
                                <p class="text-gray-500">No MDRRMO staff registered yet.</p>
                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Add First Staff Member
                                </a>
                            @endif
                        </div>
                    @endif
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
function confirmDelete(userId, userName) {
    showDeleteConfirmation(
        'Delete User',
        'Are you sure you want to delete this MDRRMO staff member?',
        userName,
        'Yes, Delete User',
        function() {
            showLoading('Deleting user...');

            fetch(`/users/${userId}`, {
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
                        location.reload();
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

function resendVerification(userId, userEmail) {
    showDeleteConfirmation(
        'Resend Verification Email',
        'Are you sure you want to resend the verification email to this user?',
        userEmail,
        'Yes, Resend Email',
        function() {
            showLoading('Sending verification email...');

            fetch(`/users/${userId}/resend-verification`, {
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

// Initialize DataTable for better UX
@if($users->count() > 0)
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 5, "desc" ]],
        "pageLength": 10,
        "responsive": true,
        "searching": false, // We have our own search
        "paging": false, // We use Laravel pagination
        "info": false
    });
});
@endif
</script>
@endpush
