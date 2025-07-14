@extends('layouts.app')

@section('title', 'Vehicle Management - MDRRMO')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Vehicle Management</h1>
            <p class="text-muted">Manage MDRRMO emergency response fleet</p>
        </div>
        @can('admin')
        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Vehicle
        </a>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Fleet
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['available'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Deployed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['deployed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-road fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['maintenance'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attention Required Panel -->
    @if($attention['maintenance']->count() > 0 || $attention['low_fuel']->count() > 0)
    <div class="alert alert-warning" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Vehicles Requiring Attention</h6>

        @if($attention['maintenance']->count() > 0)
        <div class="mb-2">
            <strong>Maintenance Due/Overdue ({{ $attention['maintenance']->count() }}):</strong>
            @foreach($attention['maintenance'] as $vehicle)
                <span class="badge bg-warning text-dark me-1">{{ $vehicle->vehicle_number }}</span>
            @endforeach
        </div>
        @endif

        @if($attention['low_fuel']->count() > 0)
        <div class="mb-0">
            <strong>Low Fuel ({{ $attention['low_fuel']->count() }}):</strong>
            @foreach($attention['low_fuel'] as $vehicle)
                <span class="badge bg-danger me-1">{{ $vehicle->vehicle_number }} ({{ number_format($vehicle->fuel_percentage, 1) }}%)</span>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Fleet Management</h6>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ $filters['search'] }}"
                           placeholder="Vehicle number, type, plate...">
                </div>

                <div class="col-md-2">
                    <label for="type" class="form-label">Vehicle Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="ambulance" {{ $filters['type'] === 'ambulance' ? 'selected' : '' }}>Ambulance</option>
                        <option value="fire_truck" {{ $filters['type'] === 'fire_truck' ? 'selected' : '' }}>Fire Truck</option>
                        <option value="rescue_vehicle" {{ $filters['type'] === 'rescue_vehicle' ? 'selected' : '' }}>Rescue Vehicle</option>
                        <option value="patrol_car" {{ $filters['type'] === 'patrol_car' ? 'selected' : '' }}>Patrol Car</option>
                        <option value="motorcycle" {{ $filters['type'] === 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                        <option value="emergency_van" {{ $filters['type'] === 'emergency_van' ? 'selected' : '' }}>Emergency Van</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="available" {{ $filters['status'] === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="deployed" {{ $filters['status'] === 'deployed' ? 'selected' : '' }}>Deployed</option>
                        <option value="maintenance" {{ $filters['status'] === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="out_of_service" {{ $filters['status'] === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="operational" class="form-label">Operational</label>
                    <select class="form-select" id="operational" name="operational">
                        <option value="">All</option>
                        <option value="1" {{ $filters['operational'] === true ? 'selected' : '' }}>Operational</option>
                        <option value="0" {{ $filters['operational'] === false ? 'selected' : '' }}>Non-Operational</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>

            <!-- Vehicles Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="vehiclesTable">
                    <thead>
                        <tr>
                            <th>Vehicle #</th>
                            <th>Type</th>
                            <th>Make/Model</th>
                            <th>Plate Number</th>
                            <th>Status</th>
                            <th>Fuel Level</th>
                            <th>Capacity</th>
                            <th>Last Maintenance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                        <tr>
                            <td>
                                <a href="{{ route('vehicles.show', $vehicle->id) }}" class="text-decoration-none">
                                    <strong>{{ $vehicle->vehicle_number }}</strong>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ str_replace('_', ' ', ucwords($vehicle->vehicle_type, '_')) }}</span>
                            </td>
                            <td>{{ $vehicle->make_model }} ({{ $vehicle->year }})</td>
                            <td>{{ $vehicle->plate_number }}</td>
                            <td>
                                <span class="status-badge-{{ $vehicle->id }}
                                    @switch($vehicle->status)
                                        @case('available') badge bg-success @break
                                        @case('deployed') badge bg-warning @break
                                        @case('maintenance') badge bg-info @break
                                        @case('out_of_service') badge bg-danger @break
                                        @default badge bg-secondary
                                    @endswitch">
                                    {{ ucwords(str_replace('_', ' ', $vehicle->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="fuel-info-{{ $vehicle->id }}">
                                    @php
                                        $fuelPercentage = $vehicle->fuel_percentage;
                                        $fuelClass = $fuelPercentage >= 75 ? 'text-success' :
                                                   ($fuelPercentage >= 50 ? 'text-warning' :
                                                   ($fuelPercentage >= 25 ? 'text-warning' : 'text-danger'));
                                    @endphp
                                    <span class="fuel-percentage-{{ $vehicle->id }} {{ $fuelClass }}">
                                        {{ number_format($fuelPercentage, 1) }}%
                                    </span>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar fuel-bar-{{ $vehicle->id }}
                                            @if($fuelPercentage >= 75) bg-success
                                            @elseif($fuelPercentage >= 50) bg-warning
                                            @elseif($fuelPercentage >= 25) bg-warning
                                            @else bg-danger @endif"
                                             style="width: {{ $fuelPercentage }}%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $vehicle->capacity }} persons</td>
                            <td>
                                @if($vehicle->last_maintenance)
                                                                            {{ $vehicle->last_maintenance_date ? $vehicle->last_maintenance_date->format('M d, Y') : 'Not scheduled' }}
                                @else
                                    <span class="text-muted">Not recorded</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('vehicles.show', $vehicle->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @can('admin')
                                    <a href="{{ route('vehicles.edit', $vehicle->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Edit Vehicle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan

                                    <!-- Quick Actions Dropdown -->
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown" title="Quick Actions">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $vehicle->id }})">
                                                <i class="fas fa-exchange-alt me-2"></i>Update Status
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateFuel({{ $vehicle->id }})">
                                                <i class="fas fa-gas-pump me-2"></i>Update Fuel
                                            </a></li>
                                            @if($vehicle->status !== 'maintenance')
                                            <li><a class="dropdown-item" href="#" onclick="scheduleMaintenance({{ $vehicle->id }})">
                                                <i class="fas fa-calendar-plus me-2"></i>Schedule Maintenance
                                            </a></li>
                                            @else
                                            <li><a class="dropdown-item" href="#" onclick="completeMaintenance({{ $vehicle->id }})">
                                                <i class="fas fa-check-circle me-2"></i>Complete Maintenance
                                            </a></li>
                                            @endif
                                            @can('admin')
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDeleteVehicle({{ $vehicle->id }}, '{{ $vehicle->vehicle_number }}')">
                                                <i class="fas fa-trash me-2"></i>Delete Vehicle
                                            </a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No vehicles found matching your criteria.</p>
                                @can('admin')
                                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add First Vehicle
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vehicles->hasPages())
            <div class="d-flex justify-content-center">
                {{ $vehicles->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals for Quick Actions -->
@include('vehicles.partials.status-modal')
@include('vehicles.partials.fuel-modal')
@include('vehicles.partials.maintenance-modal')
@include('vehicles.partials.complete-maintenance-modal')

<!-- Delete functionality now handled by SweetAlert2 -->

@endsection

@section('scripts')
<script>
// Vehicle status update function
function updateStatus(vehicleId) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    document.getElementById('statusVehicleId').value = vehicleId;
    modal.show();
}

// Vehicle fuel update function
function updateFuel(vehicleId) {
    const modal = new bootstrap.Modal(document.getElementById('fuelModal'));
    document.getElementById('fuelVehicleId').value = vehicleId;
    modal.show();
}

// Schedule maintenance function
function scheduleMaintenance(vehicleId) {
    const modal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
    document.getElementById('maintenanceVehicleId').value = vehicleId;
    modal.show();
}

// Complete maintenance function
function completeMaintenance(vehicleId) {
    const modal = new bootstrap.Modal(document.getElementById('completeMaintenanceModal'));
    document.getElementById('completeMaintenanceVehicleId').value = vehicleId;
    modal.show();
}

// Delete vehicle function
function confirmDeleteVehicle(vehicleId, vehicleNumber) {
    showDeleteConfirmation(
        'Delete Vehicle',
        'Are you sure you want to delete this vehicle?',
        vehicleNumber,
        'Yes, Delete Vehicle',
        function() {
            showLoading('Deleting vehicle...');

            fetch(`/vehicles/${vehicleId}`, {
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
                    showSuccessToast(data.message || 'Vehicle deleted successfully');
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

// AJAX handlers for quick updates
document.addEventListener('DOMContentLoaded', function() {
    // Status update form
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const vehicleId = document.getElementById('statusVehicleId').value;
        const status = document.getElementById('newStatus').value;

        fetch(`/vehicles/${vehicleId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.status-badge-${vehicleId}`).className = `status-badge-${vehicleId} ${data.vehicle.status_badge}`;
                document.querySelector(`.status-badge-${vehicleId}`).textContent = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                showToast('success', data.message);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            showToast('error', 'An error occurred while updating status');
            console.error('Error:', error);
        });
    });

    // Fuel update form
    document.getElementById('fuelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const vehicleId = document.getElementById('fuelVehicleId').value;
        const fuelLevel = document.getElementById('newFuelLevel').value;

        fetch(`/vehicles/${vehicleId}/fuel`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ fuel_level: parseFloat(fuelLevel) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const percentage = data.vehicle.fuel_percentage;
                document.querySelector(`.fuel-percentage-${vehicleId}`).textContent = `${percentage.toFixed(1)}%`;
                document.querySelector(`.fuel-percentage-${vehicleId}`).className = `fuel-percentage-${vehicleId} ${data.vehicle.fuel_status_class}`;
                document.querySelector(`.fuel-bar-${vehicleId}`).style.width = `${percentage}%`;

                // Update progress bar color
                const progressBar = document.querySelector(`.fuel-bar-${vehicleId}`);
                progressBar.className = progressBar.className.replace(/bg-(success|warning|danger)/, '');
                if (percentage >= 75) progressBar.classList.add('bg-success');
                else if (percentage >= 25) progressBar.classList.add('bg-warning');
                else progressBar.classList.add('bg-danger');

                bootstrap.Modal.getInstance(document.getElementById('fuelModal')).hide();
                showToast('success', data.message);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            showToast('error', 'An error occurred while updating fuel level');
            console.error('Error:', error);
        });
    });
});

// Toast notification function
function showToast(type, message) {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}
</script>
@endsection
