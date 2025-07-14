@extends('layouts.app')

@section('title', 'MDRRMO Staff Dashboard - Emergency Response Operations')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-user-shield me-3"></i>Staff Dashboard
            </h1>
            <p class="page-subtitle">{{ auth()->user()->full_name }} - {{ auth()->user()->position ?? 'MDRRMO Staff' }}</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Report Incident
                </a>
                <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-user me-2"></i>My Profile
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Total Staff</h5>
                            <h2 class="mb-0">{{ $stats['total_staff'] }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Active Staff</h5>
                            <h2 class="mb-0">{{ $stats['active_staff'] }}</h2>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">My Municipality</h5>
                            <h6 class="mb-0">{{ auth()->user()->municipality }}</h6>
                        </div>
                        <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Role</h5>
                            <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</h6>
                        </div>
                        <i class="fas fa-id-badge fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Emergency Response</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('incidents.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <span>View Incidents</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('incidents.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <span>Report New</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-ambulance fa-2x mb-2"></i>
                                <span>Vehicles</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('victims.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>Victims</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Analytics & Heat Map</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>Dashboard Analytics</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('heat-map.index') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                                <span>Heat Map</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-file-export fa-2x mb-2"></i>
                                <span>Export Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>My Recent Activities</h5>
                    <small class="text-muted">Last 10 activities</small>
                </div>
                <div class="card-body">
                    @if($recentActivities && $recentActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Date & Time</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</span>
                                            </td>
                                            <td>{{ $activity->description }}</td>
                                            <td>
                                                <small>{{ $activity->created_at->format('M d, Y H:i:s') }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $activity->ip_address }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activities found.</p>
                            <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Start by reporting your first incident
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom styles for this dashboard -->
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

.opacity-75 {
    opacity: 0.75;
}
</style>
@endsection
