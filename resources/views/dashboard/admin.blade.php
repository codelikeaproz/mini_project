@extends('layouts.app')

@section('title', 'Administrator Dashboard - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">MDRRMO Administrator Dashboard</h1>
            <p class="page-subtitle">
                Welcome back, {{ auth()->user()->full_name }}!
                <span class="badge bg-danger">Administrator</span>
            </p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Report Incident
                </a>
                <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Admin Actions</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('users.index') }}">
                        <i class="fas fa-users me-2"></i>Manage Staff</a></li>
                    <li><a class="dropdown-item" href="{{ route('vehicles.index') }}">
                        <i class="fas fa-truck me-2"></i>Fleet Management</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.login-attempts') }}">
                        <i class="fas fa-shield-alt me-2"></i>Security Logs</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-download me-2"></i>Export Reports</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ number_format($stats['total_incidents']) }}</h3>
                            <p class="text-muted mb-0">Total Incidents</p>
                            <small class="text-success">
                                <i class="fas fa-calendar-day"></i> Today: {{ $stats['incidents_today'] }}
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
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['pending_incidents'] }}</h3>
                            <p class="text-muted mb-0">Pending Response</p>
                            <small class="text-info">
                                <i class="fas fa-users"></i> Active: {{ $stats['responding_incidents'] }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x" style="color: var(--info);"></i>
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
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['available_vehicles'] }}/{{ $stats['total_vehicles'] }}</h3>
                            <p class="text-muted mb-0">Fleet Status</p>
                            <small class="text-success">
                                <i class="fas fa-truck"></i> Available for deployment
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ambulance fa-2x" style="color: var(--primary-500);"></i>
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
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['active_staff'] }}</h3>
                            <p class="text-muted mb-0">MDRRMO Staff</p>
                            <small class="text-muted">
                                <i class="fas fa-user-plus"></i> Total: {{ $stats['total_staff'] }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x" style="color: var(--success);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Administrative Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Quick Administrative Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>Manage Staff</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-truck fa-2x mb-2"></i>
                                <span>Fleet Management</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('incidents.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <span>All Incidents</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.login-attempts') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                <span>Security Logs</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Incident Trends Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Incident Trends</h5>
                    <small class="text-muted">Last 6 months</small>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Incident Type Distribution -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Incident Types</h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Incident Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1 text-warning">{{ $stats['pending_incidents'] }}</h4>
                                <p class="mb-0 text-muted">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1" style="color: var(--info);">{{ $stats['responding_incidents'] }}</h4>
                                <p class="mb-0 text-muted">Responding</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1 text-success">{{ $stats['resolved_incidents'] ?? 0 }}</h4>
                                <p class="mb-0 text-muted">Resolved</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1" style="color: var(--primary-600);">{{ ($stats['total_casualties'] ?? 0) + ($stats['total_injuries'] ?? 0) }}</h4>
                                <p class="mb-0 text-muted">Total Affected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fleet Status Detail -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Fleet Status Detail</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Available</span>
                        <span class="badge bg-success">{{ $vehicleStats['available'] ?? $stats['available_vehicles'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['available'] ?? $stats['available_vehicles']) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Deployed</span>
                        <span class="badge bg-primary">{{ $vehicleStats['deployed'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['deployed'] ?? 0) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Maintenance</span>
                        <span class="badge bg-warning">{{ $vehicleStats['maintenance'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['maintenance'] ?? 0) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span>Out of Service</span>
                        <span class="badge bg-danger">{{ $vehicleStats['out_of_service'] ?? 0 }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['out_of_service'] ?? 0) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Incidents</h5>
                    <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentIncidents && $recentIncidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIncidents->take(5) as $incident)
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ \Illuminate\Support\Str::limit($incident->location, 25) }}</small>
                                            </td>
                                            <td>
                                                @switch($incident->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('responding')
                                                        <span class="badge bg-info">Responding</span>
                                                        @break
                                                    @case('resolved')
                                                        <span class="badge bg-success">Resolved</span>
                                                        @break
                                                    @case('closed')
                                                        <span class="badge bg-secondary">Closed</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <small>{{ $incident->incident_datetime ? $incident->incident_datetime->format('M d, H:i') : 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent incidents recorded.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Staff Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Recent Staff Activities</h5>
                    <a href="{{ route('admin.login-attempts') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentActivities && $recentActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th>Staff Member</th>
                                        <th>Role</th>
                                        <th>Municipality</th>
                                        <th>Status</th>
                                        <th>Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities->take(5) as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initials bg-primary text-white">
                                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->full_name }}</div>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($user->isAdmin())
                                                    <span class="badge bg-danger">Admin</span>
                                                @else
                                                    <span class="badge bg-info">Staff</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $user->municipality }}</small></td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $user->created_at->format('M d, Y') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent staff activities.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Avatar -->
<style>
.avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    font-size: 0.75rem;
    font-weight: 600;
}

.avatar-sm {
    width: 1.5rem;
    height: 1.5rem;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['months'] ?? []),
                datasets: [{
                    label: 'Incidents',
                    data: @json($chartData['incidents'] ?? []),
                    borderColor: 'rgb(107, 118, 113)',
                    backgroundColor: 'rgba(107, 118, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Incident Types Chart
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: @json($typeChartData['labels'] ?? []),
                datasets: [{
                    data: @json($typeChartData['data'] ?? []),
                    backgroundColor: [
                        '#6b7671',
                        '#8fa899',
                        '#b3d4c7',
                        '#f1f5f3',
                        '#dc3545',
                        '#ffc107',
                        '#17a2b8',
                        '#28a745'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
