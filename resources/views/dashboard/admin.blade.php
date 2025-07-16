@extends('layouts.app')

@section('title', 'Administrator Dashboard - MDRRMO Maramag')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header with Emergency Response Styling -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-shield-alt text-danger fs-5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h4 mb-1 text-dark fw-bold">Administrator Dashboard</h1>
                    <p class="text-muted mb-0 small">Welcome back, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!
                        <span class="badge bg-danger bg-opacity-10 text-danger ms-2">Administrator</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="btn-lg btn-primary"></i>Admin Tools
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('users.index') }}">
                            <i class="fas fa-users me-2 text-primary"></i>Manage Staff</a></li>
                        <li><a class="dropdown-item" href="{{ route('vehicles.index') }}">
                            <i class="fas fa-truck me-2 text-primary"></i>Fleet Management</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.login-attempts') }}">
                            <i class="fas fa-shield-alt me-2 text-primary"></i>Security Logs</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-download me-2 text-primary"></i>Export Reports</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
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
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['total_incidents'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-clock text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Pending Incidents</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['pending_incidents'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-truck text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Available Vehicles</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['available_vehicles'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Active Staff</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $stats['active_staff'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Incident Trends Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-chart-line me-2"></i>Monthly Incident Trends</h6>
                        <span class="small text-muted">Last 6 months</span>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incident Type Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-chart-pie me-2"></i>Incident Types</h6>
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity - Full Width for Better Display -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-history me-2"></i>Recent Incidents</h6>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($recent_incidents) && $recent_incidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 text-muted fw-medium">Incident #</th>
                                        <th class="border-0 text-muted fw-medium">Type</th>
                                        <th class="border-0 text-muted fw-medium">Location</th>
                                        <th class="border-0 text-muted fw-medium">Severity</th>
                                        <th class="border-0 text-muted fw-medium">Status</th>
                                        <th class="border-0 text-muted fw-medium">Time</th>
                                        <th class="border-0 text-muted fw-medium">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_incidents as $incident)
                                    <tr>
                                        <td class="text-primary fw-medium">{{ $incident->incident_number }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ Str::limit($incident->location, 40) }}</td>
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
                                        <td class="text-muted">{{ $incident->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary btn-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h6 class="text-muted mt-3">No recent incidents</h6>
                            <p class="text-muted">All quiet on the emergency front.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Define chart data globally -->
<script>
window.chartData = @json($chartData ?? []);
window.typeChartData = @json($typeChartData ?? []);
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        // Use the global chart data with fallbacks
        const months = window.chartData.months || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const incidents = window.chartData.incidents || [5, 8, 12, 6, 9, 7];

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Incidents',
                    data: incidents,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
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
                        },
                        ticks: {
                            stepSize: 1,
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
    }

    // Incident Types Chart
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        // Use the global type chart data with fallbacks
        const labels = window.typeChartData.labels || ['Vehicle vs Vehicle', 'Medical Emergency', 'Vehicle vs Pedestrian', 'Transport to Hospital', 'Other'];
        const data = window.typeChartData.data || [35, 25, 15, 15, 10];

        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#0d6efd',
                        '#fd7e14',
                        '#dc3545',
                        '#198754',
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            },
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
