@extends('layouts.app')

@section('title', 'Vehicle Details - ' . $vehicle->vehicle_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Vehicle Details</h1>
                    <p class="mb-0 text-gray-600">{{ $vehicle->vehicle_number }} - {{ $vehicle->formatted_vehicle_type }}</p>
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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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
                                    @switch($vehicle->type)
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
                                        @default
                                            <i class="fas fa-car fa-4x"></i>
                                    @endswitch
                                </div>
                                <h4 class="mb-1">{{ $vehicle->vehicle_number }}</h4>
                                <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $vehicle->type)) }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Vehicle Number:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->vehicle_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Type:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $vehicle->type)) }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Model:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->model ?: 'Not specified' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Year:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->year ?: 'Not specified' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>License Plate:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->license_plate ?: 'Not specified' }}</div>
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
                                    @switch($vehicle->status)
                                        @case('available')
                                            <span class="badge badge-success">Available</span>
                                            @break
                                        @case('in_use')
                                            <span class="badge badge-primary">In Use</span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge badge-warning">Under Maintenance</span>
                                            @break
                                        @case('out_of_service')
                                            <span class="badge badge-danger">Out of Service</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ ucfirst($vehicle->status) }}</span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Current Location:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->current_location ?: 'MDRRMO Station' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Assigned Driver:</strong></div>
                                <div class="col-sm-8">{{ $vehicle->assigned_driver ?: 'Not assigned' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                <div class="col-sm-8">
                                    {{ $vehicle->updated_at->format('M d, Y g:i A') }}
                                    <br><small class="text-muted">{{ $vehicle->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @if($vehicle->notes)
                                <div class="row">
                                    <div class="col-sm-4"><strong>Notes:</strong></div>
                                    <div class="col-sm-8">{{ $vehicle->notes }}</div>
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
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><strong>Fuel Level:</strong></span>
                                    <span class="badge badge-{{ $vehicle->fuel_level >= 50 ? 'success' : ($vehicle->fuel_level >= 25 ? 'warning' : 'danger') }}">
                                        {{ $vehicle->fuel_level }}%
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar
                                        @if($vehicle->fuel_level >= 50) bg-success
                                        @elseif($vehicle->fuel_level >= 25) bg-warning
                                        @else bg-danger
                                        @endif"
                                        role="progressbar"
                                        style="width: {{ $vehicle->fuel_level }}%"
                                        aria-valuenow="{{ $vehicle->fuel_level }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Mileage:</strong></div>
                                <div class="col-sm-6">{{ number_format($vehicle->mileage) }} km</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Last Refuel:</strong></div>
                                <div class="col-sm-6">
                                    {{ $vehicle->last_refuel_date ? $vehicle->last_refuel_date->format('M d, Y') : 'Not recorded' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6"><strong>Last Refuel Amount:</strong></div>
                                <div class="col-sm-6">
                                    {{ $vehicle->last_refuel_amount ? $vehicle->last_refuel_amount . ' liters' : 'Not recorded' }}
                                </div>
                            </div>

                            @if($vehicle->fuel_level < 25)
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
                                    @if($vehicle->last_maintenance_date)
                                        {{ $vehicle->last_maintenance_date->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $vehicle->last_maintenance_date->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">No maintenance recorded</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Next Scheduled:</strong></div>
                                <div class="col-sm-6">
                                    @if($vehicle->next_maintenance_date)
                                        {{ $vehicle->next_maintenance_date->format('M d, Y') }}
                                        @if($vehicle->next_maintenance_date->isPast())
                                            <br><span class="badge badge-danger">Overdue</span>
                                        @elseif($vehicle->next_maintenance_date->diffInDays() <= 7)
                                            <br><span class="badge badge-warning">Due Soon</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not scheduled</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6"><strong>Maintenance Type:</strong></div>
                                <div class="col-sm-6">
                                    {{ $vehicle->maintenance_type ?: 'Not specified' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6"><strong>Maintenance Notes:</strong></div>
                                <div class="col-sm-6">
                                    {{ $vehicle->maintenance_notes ?: 'No notes' }}
                                </div>
                            </div>

                            @if($vehicle->next_maintenance_date && $vehicle->next_maintenance_date->isPast())
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-tools"></i>
                                    <strong>Maintenance Overdue:</strong> This vehicle requires immediate maintenance.
                                </div>
                            @elseif($vehicle->next_maintenance_date && $vehicle->next_maintenance_date->diffInDays() <= 7)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-tools"></i>
                                    <strong>Maintenance Due Soon:</strong> Schedule maintenance within {{ $vehicle->next_maintenance_date->diffInDays() }} days.
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
            @if($vehicle->incidents && $vehicle->incidents->count() > 0)
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
                                        @foreach($vehicle->incidents->take(5) as $incident)
                                            <tr>
                                                <td>{{ $incident->incident_datetime ? $incident->incident_datetime->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }}</td>
                                                <td>{{ $incident->location }}</td>
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
                                                        @case('cancelled')
                                                            <span class="badge badge-secondary">Cancelled</span>
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
    $('#current_fuel_level').val({{ $vehicle->fuel_level }});
}

function scheduleMaintenance() {
    $('#maintenanceModal').modal('show');
}

function updateStatus() {
    $('#statusModal').modal('show');
    // Pre-select current status
    $('#new_status').val('{{ $vehicle->status }}');
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

// Auto-refresh status indicators if needed
setInterval(function() {
    // Could add AJAX call to refresh real-time data
}, 30000); // 30 seconds
</script>
@endpush
