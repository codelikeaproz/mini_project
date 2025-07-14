@extends('layouts.app')

@section('title', 'MDRRMO Staff Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">MDRRMO Staff Dashboard</h1>
            <p class="mb-0 text-gray-600">{{ auth()->user()->full_name }} - {{ auth()->user()->municipality }}</p>
        </div>
        <div class="d-sm-flex align-items-center">
            <span class="badge badge-primary mr-2">MDRRMO Staff</span>
            <small class="text-gray-600">{{ now()->format('l, F d, Y') }}</small>
        </div>
    </div>

    <!-- Operational Status Cards -->
    <div class="row">
        <!-- Pending Incidents -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Response</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingIncidents->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center mt-2">
                        <div class="col-auto">
                            <small class="text-danger">
                                <i class="fas fa-clock"></i> Requires immediate attention
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Incidents -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Incidents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayIncidents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center mt-2">
                        <div class="col-auto">
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> {{ $resolvedToday }} resolved
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Vehicles -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availableVehicles }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center mt-2">
                        <div class="col-auto">
                            <small class="text-info">
                                <i class="fas fa-tools"></i> {{ $maintenanceVehicles }} in maintenance
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Assignments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                My Assignments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $myAssignments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center mt-2">
                        <div class="col-auto">
                            <small class="text-primary">
                                <i class="fas fa-tasks"></i> Active assignments
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('incidents.create') }}" class="btn btn-danger btn-block">
                                <i class="fas fa-plus-circle"></i> Report New Incident
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('incidents.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-list"></i> View All Incidents
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-truck"></i> Check Fleet Status
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('victims.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-user-injured"></i> Victim Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Pending Incidents Table -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Incidents Requiring Response</h6>
                    <a href="{{ route('incidents.create') }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-plus"></i> Report Incident
                    </a>
                </div>
                <div class="card-body">
                    @if($pendingIncidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Severity</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingIncidents->take(10) as $incident)
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }}
                                                </span>
                                            </td>
                                            <td>{{ \Illuminate\Support\Str::limit($incident->location, 25) }}</td>
                                            <td>
                                                @switch($incident->severity)
                                                    @case('critical')
                                                        <span class="badge badge-danger">Critical</span>
                                                        @break
                                                    @case('major')
                                                        <span class="badge badge-warning">Major</span>
                                                        @break
                                                    @case('minor')
                                                        <span class="badge badge-info">Minor</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($incident->severity) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small>{{ $incident->incident_datetime ? $incident->incident_datetime->format('M d, H:i') : 'N/A' }}</small>
                                                <br><small class="text-muted">{{ $incident->incident_datetime ? $incident->incident_datetime->diffForHumans() : 'Unknown time' }}</small>
                                            </td>
                                            <td>
                                                @switch($incident->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                        @break
                                                    @case('in_progress')
                                                        <span class="badge badge-primary">In Progress</span>
                                                        @break
                                                    @case('resolved')
                                                        <span class="badge badge-success">Resolved</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($incident->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('incidents.show', $incident->id) }}"
                                                       class="btn btn-sm btn-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('incidents.edit', $incident->id) }}"
                                                       class="btn btn-sm btn-warning" title="Update">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($pendingIncidents->count() > 10)
                            <div class="text-center">
                                <a href="{{ route('incidents.index') }}" class="btn btn-primary">
                                    View All {{ $pendingIncidents->count() }} Incidents
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-gray-600">No Pending Incidents</h5>
                            <p class="text-muted">All incidents have been addressed. Great work!</p>
                            <a href="{{ route('incidents.index') }}" class="btn btn-primary">View All Incidents</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-lg-4 mb-4">
            <!-- Vehicle Status Widget -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fleet Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-success"><i class="fas fa-circle"></i> Available</span>
                            <span class="font-weight-bold">{{ $availableVehicles }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-primary"><i class="fas fa-circle"></i> In Use</span>
                            <span class="font-weight-bold">{{ $deployedVehicles ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-warning"><i class="fas fa-circle"></i> Maintenance</span>
                            <span class="font-weight-bold">{{ $maintenanceVehicles }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-danger"><i class="fas fa-circle"></i> Out of Service</span>
                            <span class="font-weight-bold">{{ $outOfServiceVehicles ?? 0 }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-sm btn-primary btn-block">
                            View Fleet Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Widget -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($recentActivity && $recentActivity->count() > 0)
                        @foreach($recentActivity->take(5) as $activity)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    @switch($activity->action)
                                        @case('incident_created')
                                            <i class="fas fa-plus-circle text-primary"></i>
                                            @break
                                        @case('incident_updated')
                                            <i class="fas fa-edit text-warning"></i>
                                            @break
                                        @case('vehicle_updated')
                                            <i class="fas fa-truck text-info"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle text-secondary"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</div>
                                    <div class="small text-muted">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-2x text-gray-300 mb-2"></i>
                            <p class="small text-muted">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contacts Widget -->
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
                        <strong>Fire Department:</strong>
                        <br><a href="tel:+639123456790" class="text-danger">0912-345-6790</a>
                    </div>
                    <div class="mb-2">
                        <strong>Police Station:</strong>
                        <br><a href="tel:+639123456791" class="text-info">0912-345-6791</a>
                    </div>
                    <div class="mb-2">
                        <strong>Hospital:</strong>
                        <br><a href="tel:+639123456792" class="text-success">0912-345-6792</a>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">Emergency Hotline</small>
                        <br><strong><a href="tel:911" class="text-danger">911</a></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weather Alert (if needed) -->
    @if(isset($weatherAlert) && $weatherAlert)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Weather Alert:</strong> {{ $weatherAlert }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh pending incidents every 30 seconds
    setInterval(function() {
        // Refresh pending incidents count
        fetch('/api/dashboard/pending-count')
            .then(response => response.json())
            .then(data => {
                // Update the pending incidents count if it changed
                if (data.count !== {{ $pendingIncidents->count() }}) {
                    location.reload();
                }
            })
            .catch(error => console.log('Refresh failed:', error));
    }, 30000);

    // Add notification sound for urgent incidents
    @if($pendingIncidents->where('severity', 'critical')->count() > 0)
        // Could add audio alert for critical incidents
        console.log('Critical incidents require immediate attention!');
    @endif
});
</script>
@endpush
@endsection
