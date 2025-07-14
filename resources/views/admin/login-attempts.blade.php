@extends('layouts.app')

@section('title', 'Login Attempts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Login Attempts Management</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('admin.login-attempts') }}" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2"
                                   placeholder="Search by email, IP..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Statistics Cards -->
                    @if(isset($stats))
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h4>{{ $stats['total'] ?? 0 }}</h4>
                                    <p class="mb-0">Total Attempts</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h4>{{ $stats['successful'] ?? 0 }}</h4>
                                    <p class="mb-0">Successful</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h4>{{ $stats['failed'] ?? 0 }}</h4>
                                    <p class="mb-0">Failed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h4>{{ $stats['today'] ?? 0 }}</h4>
                                    <p class="mb-0">Today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Login Attempts Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Email</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginAttempts as $attempt)
                                <tr>
                                    <td>
                                        <small>{{ $attempt->attempted_at->format('M d, Y') }}</small><br>
                                        <strong>{{ $attempt->attempted_at->format('H:i:s') }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $attempt->email }}</strong>
                                        @if($attempt->user)
                                            <br><small class="text-muted">{{ $attempt->user->full_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $attempt->ip_address }}</code>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($attempt->user_agent, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($attempt->successful)
                                            <span class="badge bg-success">Success</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="showDetails({{ $attempt->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-2"></i>
                                            <p>No login attempts found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($loginAttempts->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $loginAttempts->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login Attempt Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDetails(attemptId) {
    // This would fetch and show details via AJAX
    $('#detailsModal').modal('show');
    $('#detailsContent').html('<p>Loading details...</p>');

    // You can implement AJAX call here to fetch details
    setTimeout(() => {
        $('#detailsContent').html('<p>Detailed information about login attempt #' + attemptId + '</p>');
    }, 500);
}
</script>
@endpush
@endsection
