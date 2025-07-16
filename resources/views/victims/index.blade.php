@extends('layouts.app')

@section('title', 'Victim Management - MDRRMO Maramag')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-injured text-primary fs-5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h4 mb-1 text-dark fw-bold">Victim Management</h1>
                    <p class="text-muted mb-0 small">Track and manage emergency victims and their medical status</p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('victims.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add New Victim
            </a>
                </div>
            </div>

    <!-- Flash Messages -->
            @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    @if(isset($stats))
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Total Victims</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-ambulance text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Critical Condition</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['critical'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-hospital text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Hospitalized</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['hospitalized'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-heart text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Stable/Recovered</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['stable'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Victims Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-table me-2"></i>All Victims</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i>Export
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
                </div>
                <div class="card-body">
                    @if($victims->count() > 0)
                        <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0" id="victimsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 text-muted small fw-medium">Name</th>
                                <th class="border-0 text-muted small fw-medium">Age</th>
                                <th class="border-0 text-muted small fw-medium">Gender</th>
                                <th class="border-0 text-muted small fw-medium">Incident</th>
                                <th class="border-0 text-muted small fw-medium">Involvement</th>
                                <th class="border-0 text-muted small fw-medium">Injury Status</th>
                                <th class="border-0 text-muted small fw-medium">Hospital</th>
                                <th class="border-0 text-muted small fw-medium">Contact</th>
                                <th class="border-0 text-muted small fw-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($victims as $victim)
                                        <tr>
                                            <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-secondary small"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium text-dark">{{ $victim->first_name }} {{ $victim->last_name }}</div>
                                        </div>
                                    </div>
                                            </td>
                                <td class="text-muted">{{ $victim->age ?? 'N/A' }}</td>
                                            <td>
                                                @if($victim->gender)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                        {{ ucfirst($victim->gender) }}
                                                    </span>
                                                @else
                                        <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($victim->incident)
                                        <a href="{{ route('incidents.show', $victim->incident) }}" class="text-decoration-none">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $victim->incident->incident_number }}
                                            </span>
                                                    </a>
                                                @else
                                        <span class="text-muted small">No incident</span>
                                                @endif
                                            </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        {{ ucwords(str_replace('_', ' ', $victim->involvement_type)) }}
                                    </span>
                                </td>
                                            <td>
                                                @switch($victim->injury_status)
                                                    @case('none')
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-check-circle me-1"></i>No Injury
                                            </span>
                                                        @break
                                                    @case('minor_injury')
                                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                                <i class="fas fa-band-aid me-1"></i>Minor Injury
                                            </span>
                                                        @break
                                                    @case('serious_injury')
                                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                                <i class="fas fa-user-injured me-1"></i>Serious Injury
                                            </span>
                                                        @break
                                                    @case('critical_condition')
                                            <span class="badge bg-danger text-white">
                                                <i class="fas fa-heartbeat me-1"></i>Critical
                                            </span>
                                            @break
                                        @case('fatal')
                                            <span class="badge bg-dark text-white">
                                                <i class="fas fa-cross me-1"></i>Fatal
                                            </span>
                                                        @break
                                                    @case('in_labor')
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                <i class="fas fa-baby me-1"></i>In Labor
                                            </span>
                                                        @break
                                                    @case('gunshot_wound')
                                            <span class="badge bg-danger text-white">
                                                <i class="fas fa-bullseye me-1"></i>Gunshot
                                            </span>
                                                        @break
                                                    @case('stab_wound')
                                            <span class="badge bg-danger text-white">
                                                <i class="fas fa-cut me-1"></i>Stab Wound
                                            </span>
                                                        @break
                                                    @default
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                {{ ucwords(str_replace('_', ' ', $victim->injury_status)) }}
                                            </span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($victim->hospital_referred)
                                        <div class="text-dark fw-medium">{{ $victim->hospital_referred }}</div>
                                        @if($victim->hospital_arrival_time)
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($victim->hospital_arrival_time)->format('M j, Y g:i A') }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted small">Not referred</span>
                                    @endif
                                </td>
                                <td>
                                    @if($victim->contact_number)
                                        <a href="tel:{{ $victim->contact_number }}" class="text-decoration-none">
                                            <i class="fas fa-phone text-success me-1"></i>{{ $victim->contact_number }}
                                        </a>
                                                @else
                                        <span class="text-muted small">No contact</span>
                                                @endif
                                            </td>
                                            <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('victims.show', $victim) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                        <a href="{{ route('victims.edit', $victim) }}" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteVictim({{ $victim->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                @if($victims->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                            {{ $victims->links() }}
                        </div>
                @endif
                    @else
                <div class="text-center py-5">
                    <i class="fas fa-user-injured text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h5 class="text-muted mt-3">No Victims Found</h5>
                    <p class="text-muted">No victim records have been created yet.</p>
                            <a href="{{ route('victims.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add First Victim
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

<script>
// Export to Excel function
function exportToExcel() {
    // Implementation for Excel export
    showAlert('Export functionality coming soon', 'info');
}

// Refresh table function
function refreshTable() {
    window.location.reload();
}

// Delete victim function
function deleteVictim(victimId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/victims/${victimId}`;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = '{{ csrf_token() }}';

            form.appendChild(methodInput);
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any tooltips if needed
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

@push('styles')
<style>
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.badge {
    font-weight: 500;
    border-radius: 6px;
}

.btn-sm {
    border-radius: 4px;
}

.card {
    transition: all 0.2s ease;
}

.table td {
    vertical-align: middle;
}

.pagination {
    --bs-pagination-color: #6c757d;
    --bs-pagination-hover-color: #0d6efd;
    --bs-pagination-hover-bg: rgba(13, 110, 253, 0.1);
}
</style>
@endpush
