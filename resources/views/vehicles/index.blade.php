@extends('layouts.app')

@section('title', 'Vehicle Management - MDRRMO Maramag')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header with Emergency Response Styling -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-truck text-primary fs-5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h4 mb-1 text-dark fw-bold">Fleet Management</h1>
                    <p class="text-muted mb-0 small">Manage MDRRMO emergency response vehicles</p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            @can('admin')
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Add Vehicle
            </a>
            @endcan
        </div>
    </div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-truck fa-lg text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold text-dark mb-1">{{ $statistics['total'] }}</h3>
                            <p class="text-muted small mb-0">Total Fleet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-lg text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold text-dark mb-1">{{ $statistics['available'] }}</h3>
                            <p class="text-muted small mb-0">Available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-road fa-lg text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold text-dark mb-1">{{ $statistics['deployed'] }}</h3>
                            <p class="text-muted small mb-0">Deployed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-tools fa-lg text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold text-dark mb-1">{{ $statistics['maintenance'] }}</h3>
                            <p class="text-muted small mb-0">Maintenance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attention Required Panel -->
    @if($attention['maintenance']->count() > 0 || $attention['low_fuel']->count() > 0)
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center mb-2">
            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                <i class="fas fa-exclamation-triangle text-warning"></i>
            </div>
            <h6 class="alert-heading mb-0 text-dark">Vehicles Requiring Attention</h6>
        </div>

        @if($attention['maintenance']->count() > 0)
        <div class="mb-2">
            <strong class="text-dark">Maintenance Due/Overdue ({{ $attention['maintenance']->count() }}):</strong>
            @foreach($attention['maintenance'] as $vehicle)
                <span class="badge bg-warning text-dark me-1">{{ $vehicle->vehicle_number }}</span>
            @endforeach
        </div>
        @endif

        @if($attention['low_fuel']->count() > 0)
        <div class="mb-0">
            <strong class="text-dark">Low Fuel ({{ $attention['low_fuel']->count() }}):</strong>
            @foreach($attention['low_fuel'] as $vehicle)
                <span class="badge bg-danger me-1">{{ $vehicle->vehicle_number }} ({{ number_format($vehicle->fuel_percentage, 1) }}%)</span>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 text-dark">
                <i class="fas fa-filter me-2 text-primary"></i>Fleet Management
            </h6>
        </div>
        <div class="card-body p-4">
            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="search" class="form-label text-muted small fw-medium">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ $filters['search'] }}"
                           placeholder="Vehicle number, type, plate...">
                </div>

                <div class="col-md-2">
                    <label for="type" class="form-label text-muted small fw-medium">Vehicle Type</label>
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
                    <label for="status" class="form-label text-muted small fw-medium">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="available" {{ $filters['status'] === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="deployed" {{ $filters['status'] === 'deployed' ? 'selected' : '' }}>Deployed</option>
                        <option value="maintenance" {{ $filters['status'] === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="out_of_service" {{ $filters['status'] === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="operational" class="form-label text-muted small fw-medium">Operational</label>
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
                <table class="table table-hover mb-0" id="vehiclesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 text-muted small fw-medium px-3 py-3">Vehicle #</th>
                            <th class="border-0 text-muted small fw-medium">Type</th>
                            <th class="border-0 text-muted small fw-medium">Make/Model</th>
                            <th class="border-0 text-muted small fw-medium">Plate Number</th>
                            <th class="border-0 text-muted small fw-medium">Assigned Driver</th>
                            <th class="border-0 text-muted small fw-medium">Status</th>
                            <th class="border-0 text-muted small fw-medium">Fuel Level</th>
                            <th class="border-0 text-muted small fw-medium">Capacity</th>
                            <th class="border-0 text-muted small fw-medium text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                        <tr class="border-bottom">
                            <td class="px-3 py-3">
                                <a href="{{ route('vehicles.show', $vehicle->id) }}" class="text-decoration-none fw-medium text-dark">
                                    {{ $vehicle->vehicle_number }}
                                </a>
                            </td>
                            <td class="py-3">
                                @php
                                    $typeColors = [
                                        'ambulance' => 'bg-danger',
                                        'fire_truck' => 'bg-warning',
                                        'rescue_vehicle' => 'bg-info',
                                        'patrol_car' => 'bg-primary',
                                        'motorcycle' => 'bg-success',
                                        'emergency_van' => 'bg-secondary'
                                    ];
                                    $typeColor = $typeColors[$vehicle->vehicle_type] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $typeColor }} bg-opacity-10 text-dark border border-{{ str_replace('bg-', '', $typeColor) }} border-opacity-25">
                                    {{ str_replace('_', ' ', ucwords($vehicle->vehicle_type, '_')) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="text-dark small">{{ $vehicle->make_model }}</div>
                                <div class="text-muted small">({{ $vehicle->year }})</div>
                            </td>
                            <td class="py-3">
                                <span class="fw-medium text-dark">{{ $vehicle->plate_number }}</span>
                            </td>
                            <td class="py-3">
                                @if($vehicle->assigned_driver_name)
                                    <div class="small text-dark">{{ $vehicle->assigned_driver_name }}</div>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-3">
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
                            <td class="py-3">
                                <div class="fuel-info-{{ $vehicle->id }}">
                                    @php
                                        $fuelPercentage = $vehicle->fuel_percentage;
                                        $fuelClass = $fuelPercentage >= 75 ? 'text-success' :
                                                   ($fuelPercentage >= 50 ? 'text-warning' :
                                                   ($fuelPercentage >= 25 ? 'text-warning' : 'text-danger'));
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <span class="fuel-percentage-{{ $vehicle->id }} {{ $fuelClass }} small fw-medium me-2">
                                            {{ number_format($fuelPercentage, 1) }}%
                                        </span>
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar fuel-bar-{{ $vehicle->id }}
                                                @if($fuelPercentage >= 75) bg-success
                                                @elseif($fuelPercentage >= 50) bg-warning
                                                @elseif($fuelPercentage >= 25) bg-warning
                                                @else bg-danger @endif"
                                                 style="width: {{ $fuelPercentage }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="text-dark small">{{ $vehicle->capacity }} persons</span>
                            </td>
                            <td class="py-3 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View Button -->
                                    <a href="{{ route('vehicles.show', $vehicle->id) }}"
                                       class="btn btn-outline-primary btn-sm"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @can('admin')
                                    <!-- Edit Button -->
                                    <a href="{{ route('vehicles.edit', $vehicle->id) }}"
                                       class="btn btn-outline-secondary btn-sm"
                                       title="Edit Vehicle">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Status Button -->
                                    <button type="button"
                                            class="btn btn-outline-info btn-sm"
                                            title="Update Status"
                                            onclick="updateStatus('{{ $vehicle->id }}', '{{ $vehicle->vehicle_number }}', '{{ $vehicle->status }}')">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>

                                    <!-- Fuel Button -->
                                    <button type="button"
                                            class="btn btn-outline-warning btn-sm"
                                            title="Update Fuel"
                                            onclick="updateFuel('{{ $vehicle->id }}', '{{ $vehicle->vehicle_number }}', {{ $vehicle->current_fuel }}, {{ $vehicle->fuel_capacity }})">
                                        <i class="fas fa-gas-pump"></i>
                                    </button>

                                    <!-- Maintenance Button -->
                                    <button type="button"
                                            class="btn btn-outline-success btn-sm"
                                            title="Maintenance Log"
                                            onclick="logMaintenance('{{ $vehicle->id }}', '{{ $vehicle->vehicle_number }}')">
                                        <i class="fas fa-wrench"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="bg-light rounded-circle p-4 d-inline-flex mb-3">
                                    <i class="fas fa-truck fa-2x text-muted"></i>
                                </div>
                                <h6 class="text-muted">No vehicles found</h6>
                                <p class="text-muted small mb-0">Try adjusting your search filters or add a new vehicle.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($vehicles->hasPages())
        <div class="card-footer bg-light border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $vehicles->firstItem() }} to {{ $vehicles->lastItem() }} of {{ $vehicles->total() }} vehicles
                </div>
                <div>
                    {{ $vehicles->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
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
function updateStatus(vehicleId, vehicleNumber, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    document.getElementById('statusVehicleId').value = vehicleId;
    document.getElementById('currentStatus').value = currentStatus;
    document.getElementById('vehicleNumber').textContent = vehicleNumber;
    modal.show();
}

// Vehicle fuel update function
function updateFuel(vehicleId, vehicleNumber, currentFuel, fuelCapacity) {
    const modal = new bootstrap.Modal(document.getElementById('fuelModal'));
    document.getElementById('fuelVehicleId').value = vehicleId;
    document.getElementById('currentFuel').value = currentFuel;
    document.getElementById('fuelCapacity').value = fuelCapacity;
    document.getElementById('vehicleNumber').textContent = vehicleNumber;
    modal.show();
}

// Schedule maintenance function
function scheduleMaintenance(vehicleId, vehicleNumber) {
    const modal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
    document.getElementById('maintenanceVehicleId').value = vehicleId;
    document.getElementById('vehicleNumber').textContent = vehicleNumber;
    modal.show();
}

// Complete maintenance function
function completeMaintenance(vehicleId, vehicleNumber) {
    const modal = new bootstrap.Modal(document.getElementById('completeMaintenanceModal'));
    document.getElementById('completeMaintenanceVehicleId').value = vehicleId;
    document.getElementById('vehicleNumber').textContent = vehicleNumber;
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
