@extends('layouts.app')

@section('title', 'MDRRMO Staff Dashboard - Emergency Response System')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">MDRRMO Staff Dashboard</h1>
            <p class="page-subtitle">Emergency Response Operations - {{ auth()->user()->municipality }}</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Report New Incident
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('vehicles.index') }}">
                            <i class="fas fa-ambulance me-2"></i>View Vehicles</a></li>
                        <li><a class="dropdown-item" href="{{ route('victims.index') }}">
                            <i class="fas fa-users me-2"></i>View Victims</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                            <i class="fas fa-user me-2"></i>My Profile</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Staff Performance Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $myStats['total_assigned'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">My Total Assignments</p>
                            <small class="text-info">
                                <i class="fas fa-user-check"></i> All time
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x" style="color: var(--primary-500);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $myStats['pending_assigned'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Pending My Response</p>
                            <small class="text-warning">
                                <i class="fas fa-clock"></i> Immediate attention needed
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x" style="color: var(--warning);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $systemStats['available_vehicles'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Available Vehicles</p>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> Ready for deployment
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ambulance fa-2x" style="color: var(--success);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $systemStats['incidents_today'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Incidents Today</p>
                            <small class="text-info">
                                <i class="fas fa-calendar-day"></i> System-wide
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x" style="color: var(--info);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Assignments & System Overview -->
    <div class="row mb-4">
        <!-- My Current Assignments -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>My Current Assignments</h5>
                    <span class="badge" style="background-color: var(--primary-500);">{{ $myIncidents->count() }} Active</span>
                </div>
                <div class="card-body">
                    @if($myIncidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Incident #</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myIncidents->take(10) as $incident)
                                        <tr>
                                            <td><strong>{{ $incident->incident_number }}</strong></td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }}
                                                </span>
                                            </td>
                                            <td>{{ $incident->location }}</td>
                                            <td>
                                                @switch($incident->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('responding')
                                                        <span class="badge" style="background-color: var(--info);">Responding</span>
                                                        @break
                                                    @case('resolved')
                                                        <span class="badge bg-success">Resolved</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($incident->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $incident->incident_datetime->format('M d, H:i') }}</td>
                                            <td>
                                                <a href="{{ route('incidents.show', $incident) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($myIncidents->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('incidents.index') }}?assigned_staff={{ auth()->id() }}"
                                   class="btn btn-outline-secondary">
                                    View All My Assignments ({{ $myIncidents->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                            <h5>No Current Assignments</h5>
                            <p class="text-muted">You have no pending incident assignments. Great work!</p>
                            <a href="{{ route('incidents.index') }}" class="btn btn-outline-primary">
                                View All System Incidents
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick System Overview -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>System Status</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <h5 class="mb-1" style="color: var(--warning);">{{ $systemStats['pending_incidents'] ?? 0 }}</h5>
                                <small class="text-muted">Pending System-wide</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <h5 class="mb-1" style="color: var(--info);">{{ $deployedVehicles ?? 0 }}</h5>
                                <small class="text-muted">Deployed Vehicles</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <h5 class="mb-1" style="color: var(--danger);">{{ $maintenanceVehicles ?? 0 }}</h5>
                                <small class="text-muted">In Maintenance</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <h5 class="mb-1" style="color: var(--success);">{{ $myStats['resolved_assigned'] ?? 0 }}</h5>
                                <small class="text-muted">My Resolved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>My Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        <div class="timeline" style="max-height: 300px; overflow-y: auto;">
                            @foreach($recentActivity->take(5) as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        <p class="mb-0 small">{{ $activity->description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent activity</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Emergency Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('incidents.create') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-exclamation-triangle me-2"></i>Report Emergency
                        </a>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-ambulance me-2"></i>Check Vehicle Status
                        </a>
                        <a href="{{ route('incidents.index') }}?status=pending" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-clock me-2"></i>View Pending Incidents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System-wide Pending Incidents (for situational awareness) -->
    @if($pendingIncidents->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>System-wide Pending Incidents</h5>
                    <span class="badge bg-warning">{{ $pendingIncidents->count() }} Pending</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Incident #</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Reported</th>
                                    <th>Assigned Staff</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingIncidents->take(5) as $incident)
                                    <tr>
                                        <td><strong>{{ $incident->incident_number }}</strong></td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $incident->location }}</td>
                                        <td>{{ $incident->incident_datetime->diffForHumans() }}</td>
                                        <td>
                                            @if($incident->assignedStaff)
                                                {{ $incident->assignedStaff->full_name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('incidents.show', $incident) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pendingIncidents->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('incidents.index') }}?status=pending" class="btn btn-outline-warning">
                                View All Pending Incidents ({{ $pendingIncidents->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-300);
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 1rem;
}

.timeline-marker {
    position: absolute;
    left: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--gray-300);
}

.timeline-content {
    background: var(--gray-50);
    border-radius: 0.375rem;
    padding: 0.5rem;
}
</style>
@endsection
