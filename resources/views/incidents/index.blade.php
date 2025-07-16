@extends('layouts.app')

@section('title', 'Incident Management - MDRRMO Maramag')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header with Emergency Response Styling -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h4 mb-1 text-dark fw-bold">Incident Management</h1>
                    <p class="text-muted mb-0 small">Monitor and manage emergency incidents in Maramag, Bukidnon</p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="toggleFilters()">
                    <i class="fas fa-filter me-1"></i>Filters
                </button>
                <a href="/heat-map" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-map-marked-alt me-1"></i>Heat Map
                </a>

            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Total Incidents</div>
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
                                <i class="fas fa-clock text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Pending</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['pending'] ?? 0 }}</div>
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
                            <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-ambulance text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Responding</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['responding'] ?? 0 }}</div>
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
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Resolved</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['resolved'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Panel (Initially Hidden) -->
    <div class="row mb-4" id="filterPanel" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-filter me-2"></i>Filter Controls</h6>
                </div>
                <div class="card-body bg-white">
                    <form method="GET" action="{{ route('incidents.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-medium">Emergency Type</label>
                                <select class="form-select form-select-sm" name="type">
                                    <option value="">All Types</option>
                                    <option value="vehicle_vs_vehicle">Vehicle Collision</option>
                                    <option value="vehicle_vs_pedestrian">Vehicle vs Pedestrian</option>
                                    <option value="vehicle_vs_animals">Vehicle vs Animals</option>
                                    <option value="vehicle_vs_property">Vehicle vs Property</option>
                                    <option value="vehicle_alone">Single Vehicle</option>
                                    <option value="maternity">Medical Emergency</option>
                                    <option value="stabbing_shooting">Violence Emergency</option>
                                    <option value="transport_to_hospital">Medical Transport</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-medium">Severity Level</label>
                                <select class="form-select form-select-sm" name="severity">
                                    <option value="">All Levels</option>
                                    <option value="minor">Minor</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-medium">Status</label>
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="responding">Responding</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-medium">Date From</label>
                                <input type="date" class="form-control form-control-sm" name="date_from">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-medium">Actions</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search me-1"></i>Apply
                                    </button>
                                    <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-list me-2"></i>Emergency Incidents</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Export
                            </button>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($incidents) && $incidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 text-muted small fw-medium">Incident #</th>
                                        <th class="border-0 text-muted small fw-medium">Type</th>
                                        <th class="border-0 text-muted small fw-medium">Location</th>
                                        <th class="border-0 text-muted small fw-medium">Severity</th>
                                        <th class="border-0 text-muted small fw-medium">Status</th>
                                        <th class="border-0 text-muted small fw-medium">Date</th>
                                        <th class="border-0 text-muted small fw-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidents as $incident)
                                    <tr>
                                        <td class="text-primary fw-medium">{{ $incident->incident_number }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ Str::limit($incident->location, 30) }}</td>
                                        <td>
                                            @switch($incident->severity_level)
                                                @case('minor')
                                                    <span class="badge bg-info bg-opacity-10 text-info">Minor</span>
                                                    @break
                                                @case('moderate')
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">Moderate</span>
                                                    @break
                                                @case('severe')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Severe</span>
                                                    @break
                                                @case('critical')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">Critical</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($incident->status)
                                                @case('pending')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                                    @break
                                                @case('responding')
                                                    <span class="badge bg-info bg-opacity-10 text-info">Responding</span>
                                                    @break
                                                @case('resolved')
                                                    <span class="badge bg-success bg-opacity-10 text-success">Resolved</span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Closed</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="text-muted small">{{ $incident->incident_datetime->format('M j, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('update', $incident)
                                                <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($incidents->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $incidents->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle text-muted fs-1 mb-3"></i>
                            <h6 class="text-muted">No incidents found</h6>
                            <p class="text-muted small">
                                @if(request()->hasAny(['type', 'severity', 'status', 'date_from']))
                                    Try adjusting your filters or <a href="{{ route('incidents.index') }}" class="text-decoration-none">clear all filters</a>.
                                @else
                                    Start by <a href="{{ route('incidents.create') }}" class="text-decoration-none">reporting the first incident</a>.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFilters() {
    const panel = document.getElementById('filterPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection
