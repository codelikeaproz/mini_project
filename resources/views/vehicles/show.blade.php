@extends('layouts.app')

@section('title', 'Vehicle Details - ' . $vehicle->vehicle_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                                                    <h1 class="h3 mb-0 text-gray-800">Vehicle Details (Updated: {{ now()->format('H:i:s') }})</h1>
                    <p class="mb-0 text-gray-600">{{ $vehicle->vehicle_number }} - {{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</p>
                </div>
                <div class="d-sm-flex">
                    <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-warning shadow-sm mr-2">
                        <i class="fas fa-edit fa-sm text-white-50"></i> Edit Vehicle
                    </a>
                    @can('admin')
                        <button type="button" class="btn btn-danger shadow-sm mr-2" onclick="confirmDeleteVehicle()">
                            <i class="fas fa-trash fa-sm text-white-50"></i> Delete Vehicle
                        </button>
                    @endcan
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Fleet
                    </a>
                </div>
            </div>



            <div class="row">
                <!-- Vehicle Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Vehicle Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="vehicle-icon mx-auto mb-3">
                                    @switch($vehicle->vehicle_type)
                                        @case('ambulance')
                                            <i class="fas fa-ambulance text-danger fa-4x"></i>
                                            @break
                                        @case('fire_truck')
                                            <i class="fas fa-fire-alt text-warning fa-4x"></i>
                                            @break
                                        @case('rescue_vehicle')
                                            <i class="fas fa-truck text-primary fa-4x"></i>
                                            @break
                                        @case('patrol_car')
                                            <i class="fas fa-car text-info fa-4x"></i>
                                            @break
                                        @case('motorcycle')
                                            <i class="fas fa-motorcycle text-success fa-4x"></i>
                                            @break
                                        @case('emergency_van')
                                            <i class="fas fa-shuttle-van text-warning fa-4x"></i>
                                            @break
                                        @default
                                            <i class="fas fa-car fa-4x"></i>
                                    @endswitch
                                </div>
                                <h4 class="mb-1">{{ $vehicle->vehicle_number }}</h4>
                                <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Vehicle Number:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->vehicle_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Type:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Make/Model:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->make_model ?: 'Not specified' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Year:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->year ?: 'Not specified' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>License Plate:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->plate_number ?: 'Not specified' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Capacity:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->capacity }} {{ $vehicle->capacity === 1 ? 'person' : 'people' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Operational:</strong></div>
                                <div class="col-sm-8">
                                    @if($vehicle->is_operational)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-warning">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><strong>Added to Fleet:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->created_at->format('F d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Current Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <!-- Debug: Status value is: {{ $vehicle->status }} -->
                                    <span class="badge badge-primary" style="background-color: red !important; color: white !important;">DEPLOYED ({{ $vehicle->status }})</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Current Location:</strong></div>
                                <div class="col-sm-8">MDRRMO Station</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Assigned Driver:</strong></div>
                                <div class="col-sm-8">
                                    <!-- Debug: Driver value is: {{ $vehicle->assigned_driver_name }} -->
                                    <span class="badge badge-info" style="background-color: blue !important; color: white !important;">DRIVER: {{ $vehicle->assigned_driver_name ?: 'NONE' }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                <div class="col-sm-8">
                                    {{ $vehicle->updated_at->format('M d, Y g:i A') }}
                                    <br><small class="text-muted">{{ $vehicle->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @if($vehicle->equipment_list)
                                <div class="row">
                                    <div class="col-sm-4"><strong>Equipment:</strong></div>
                                    <div class="col-sm-8">{{ $vehicle->equipment_list }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Fuel Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Fuel Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                @php
                                    $fuelPercentage = $vehicle->fuel_capacity > 0 ? round(($vehicle->current_fuel / $vehicle->fuel_capacity) * 100) : 0;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><strong>Fuel Level:</strong></span>
                                    <span class="badge badge-{{ $fuelPercentage >= 50 ? 'success' : ($fuelPercentage >= 25 ? 'warning' : 'danger') }}">
                                        {{ $fuelPercentage }}%
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar
                                        @if($fuelPercentage >= 50) bg-success
                                        @elseif($fuelPercentage >= 25) bg-warning
                                        @else bg-danger
                                        @endif"
                                        role="progressbar"
                                        style="width: {{ $fuelPercentage }}%"
                                        aria-valuenow="{{ $fuelPercentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Current Fuel:</strong></div>
                                <div class="col-sm-6">{{ $vehicle->current_fuel }} / {{ $vehicle->fuel_capacity }} liters</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Odometer Reading:</strong></div>
                                <div class="col-sm-6">{{ number_format($vehicle->odometer_reading) }} km</div>
                            </div>

                            @if($fuelPercentage < 25)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Low Fuel Alert:</strong> This vehicle needs refueling.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Maintenance Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Maintenance Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Last Maintenance:</strong></div>
                                <div class="col-sm-6">
                                    @if($vehicle->last_maintenance)
                                        {{ $vehicle->last_maintenance->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $vehicle->last_maintenance->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">No maintenance recorded</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Next Scheduled:</strong></div>
                                <div class="col-sm-6">
                                    @if($vehicle->next_maintenance_due)
                                        {{ $vehicle->next_maintenance_due->format('M d, Y') }}
                                        @if($vehicle->next_maintenance_due->isPast())
                                            <br><span class="badge badge-danger">Overdue</span>
                                        @elseif($vehicle->next_maintenance_due->diffInDays() <= 7)
                                            <br><span class="badge badge-warning">Due Soon</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not scheduled</span>
                                    @endif
                                </div>
                            </div>

                            @if($vehicle->next_maintenance_due && $vehicle->next_maintenance_due->isPast())
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-tools"></i>
                                    <strong>Maintenance Overdue:</strong> This vehicle requires immediate maintenance.
                                </div>
                            @elseif($vehicle->next_maintenance_due && $vehicle->next_maintenance_due->diffInDays() <= 7)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-tools"></i>
                                    <strong>Maintenance Due Soon:</strong> Schedule maintenance within {{ $vehicle->next_maintenance_due->diffInDays() }} days.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <button type="button" class="btn btn-info btn-block" onclick="updateFuel()">
                                        <i class="fas fa-gas-pump"></i> Update Fuel
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="button" class="btn btn-warning btn-block" onclick="scheduleMaintenance()">
                                        <i class="fas fa-tools"></i> Schedule Maintenance
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="button" class="btn btn-primary btn-block" onclick="updateStatus()">
                                        <i class="fas fa-exchange-alt"></i> Change Status
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-edit"></i> Edit Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Incidents Card -->
            @if(isset($deploymentHistory) && $deploymentHistory->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Incident Assignments</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Incident Type</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deploymentHistory->take(5) as $incident)
                                            <tr>
                                                <td>{{ $incident->incident_datetime ? $incident->incident_datetime->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }}</td>
                                                <td>{{ $incident->location }}</td>
                                                <td>
                                                    @switch($incident->status)
                                                        @case('pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                            @break
                                                        @case('responding')
                                                            <span class="badge badge-primary">Responding</span>
                                                            @break
                                                        @case('resolved')
                                                            <span class="badge badge-success">Resolved</span>
                                                            @break
                                                        @case('closed')
                                                            <span class="badge badge-secondary">Closed</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Include modals from partials -->
@include('vehicles.partials.fuel-modal')
@include('vehicles.partials.maintenance-modal')
@include('vehicles.partials.status-modal')

<!-- Delete functionality now handled by SweetAlert2 -->
@endsection

@push('scripts')
<script>
function updateFuel() {
    $('#fuelModal').modal('show');
    // Pre-populate current fuel level
    $('#current_fuel_level').val({{ $vehicle->current_fuel }});
}

function scheduleMaintenance() {
    $('#maintenanceModal').modal('show');
}

function updateStatus() {
    $('#statusModal').modal('show');
    // Pre-select current status
    $('#newStatus').val('{{ $vehicle->status }}');
    $('#statusVehicleId').val('{{ $vehicle->id }}');
}

function confirmDeleteVehicle() {
    showDeleteConfirmation(
        'Delete Vehicle',
        'Are you sure you want to delete this vehicle?',
        '{{ $vehicle->vehicle_number }}',
        'Yes, Delete Vehicle',
        function() {
            showLoading('Deleting vehicle...');

            fetch(`/vehicles/{{ $vehicle->id }}`, {
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
                        window.location.href = '/vehicles';
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

// Handle status form submission
$('#statusForm').on('submit', function(e) {
    e.preventDefault();

    const vehicleId = $('#statusVehicleId').val();
    const newStatus = $('#newStatus').val();

    if (!newStatus) {
        showErrorToast('Please select a status');
        return;
    }

    showLoading('Updating status...');

    fetch(`/vehicles/${vehicleId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status: newStatus
        })
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
            $('#statusModal').modal('hide');
            showSuccessToast(data.message || 'Status updated successfully');
            // Reload page to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showErrorToast(data.message || 'Update failed');
        }
    })
    .catch(error => {
        closeLoading();
        console.error('Status update error:', error);
        showErrorToast('Update failed: ' + error.message);
    });
});

// Auto-refresh status indicators if needed
setInterval(function() {
    // Could add AJAX call to refresh real-time data
}, 30000); // 30 seconds
</script>
@endpush
