@extends('layouts.app')

@section('title', 'Edit Vehicle - ' . $vehicle->vehicle_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit Vehicle</h1>
                <div class="d-sm-flex">
                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-info shadow-sm mr-2">
                        <i class="fas fa-eye fa-sm text-white-50"></i> View Details
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Fleet
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Vehicle Information - {{ $vehicle->vehicle_number }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Vehicle Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_number" class="form-label">
                                    <strong>Vehicle Number <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('vehicle_number') is-invalid @enderror"
                                       id="vehicle_number" name="vehicle_number"
                                       value="{{ old('vehicle_number', $vehicle->vehicle_number) }}" required>
                                @error('vehicle_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle_type" class="form-label">
                                    <strong>Vehicle Type <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('vehicle_type') is-invalid @enderror"
                                        id="vehicle_type" name="vehicle_type" required>
                                    <option value="">Select Type...</option>
                                    @foreach($vehicleTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('vehicle_type', $vehicle->vehicle_type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="make_model" class="form-label">
                                    <strong>Make/Model <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('make_model') is-invalid @enderror"
                                       id="make_model" name="make_model"
                                       value="{{ old('make_model', $vehicle->make_model) }}"
                                       placeholder="e.g., Toyota Hiace, Isuzu NPR" required>
                                @error('make_model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="year" class="form-label">
                                    <strong>Year <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('year') is-invalid @enderror"
                                       id="year" name="year"
                                       value="{{ old('year', $vehicle->year) }}"
                                       min="1990" max="{{ date('Y') + 1 }}" required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="plate_number" class="form-label">
                                    <strong>License Plate <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('plate_number') is-invalid @enderror"
                                       id="plate_number" name="plate_number"
                                       value="{{ old('plate_number', $vehicle->plate_number) }}"
                                       placeholder="e.g., ABC-1234" required>
                                @error('plate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Vehicle Specifications -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label">
                                    <strong>Passenger Capacity <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('capacity') is-invalid @enderror"
                                       id="capacity" name="capacity"
                                       value="{{ old('capacity', $vehicle->capacity) }}"
                                       min="1" max="50" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Number of people the vehicle can carry</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fuel_capacity" class="form-label">
                                    <strong>Fuel Tank Capacity (liters) <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('fuel_capacity') is-invalid @enderror"
                                       id="fuel_capacity" name="fuel_capacity"
                                       value="{{ old('fuel_capacity', $vehicle->fuel_capacity) }}"
                                       min="1" step="0.1" required>
                                @error('fuel_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Maximum fuel tank capacity</small>
                            </div>
                        </div>

                        <!-- Status and Assignment -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Status & Assignment</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">
                                    <strong>Current Status <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status...</option>
                                    @foreach($statusOptions as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $vehicle->status) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="assigned_driver_name" class="form-label">
                                    <strong>Assigned Driver</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('assigned_driver_name') is-invalid @enderror"
                                       id="assigned_driver_name" name="assigned_driver_name"
                                       value="{{ old('assigned_driver_name', $vehicle->assigned_driver_name) }}"
                                       placeholder="e.g., Officer Juan Santos">
                                @error('assigned_driver_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Name of the primary driver responsible for this vehicle
                                </small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox"
                                           class="form-check-input @error('is_operational') is-invalid @enderror"
                                           id="is_operational" name="is_operational"
                                           value="1" {{ old('is_operational', $vehicle->is_operational) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_operational">
                                        <strong>Vehicle is Operational</strong>
                                    </label>
                                    @error('is_operational')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Uncheck if vehicle is temporarily out of service
                                    </small>
                                </div>
                            </div>
                        </div>



                        <!-- Fuel Information -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Fuel Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_fuel" class="form-label">
                                    <strong>Current Fuel (liters)</strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('current_fuel') is-invalid @enderror"
                                       id="current_fuel" name="current_fuel"
                                       value="{{ old('current_fuel', $vehicle->current_fuel) }}"
                                       min="0" step="0.1">
                                @error('current_fuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Maximum: {{ $vehicle->fuel_capacity }} liters
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="odometer_reading" class="form-label">
                                    <strong>Odometer Reading (km)</strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('odometer_reading') is-invalid @enderror"
                                       id="odometer_reading" name="odometer_reading"
                                       value="{{ old('odometer_reading', $vehicle->odometer_reading) }}"
                                       min="0">
                                @error('odometer_reading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <!-- Maintenance Information -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Maintenance Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_maintenance" class="form-label">
                                    <strong>Last Maintenance Date</strong>
                                </label>
                                <input type="date"
                                       class="form-control @error('last_maintenance') is-invalid @enderror"
                                       id="last_maintenance" name="last_maintenance"
                                       value="{{ old('last_maintenance', $vehicle->last_maintenance?->format('Y-m-d')) }}">
                                @error('last_maintenance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="next_maintenance_due" class="form-label">
                                    <strong>Next Scheduled Maintenance</strong>
                                </label>
                                <input type="date"
                                       class="form-control @error('next_maintenance_due') is-invalid @enderror"
                                       id="next_maintenance_due" name="next_maintenance_due"
                                       value="{{ old('next_maintenance_due', $vehicle->next_maintenance_due?->format('Y-m-d')) }}">
                                @error('next_maintenance_due')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <!-- Additional Notes -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Additional Information</h5>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="equipment_list" class="form-label">
                                    <strong>Equipment List</strong>
                                </label>
                                <textarea class="form-control @error('equipment_list') is-invalid @enderror"
                                          id="equipment_list" name="equipment_list" rows="3"
                                          placeholder="List of equipment and tools available in this vehicle">{{ old('equipment_list', $vehicle->equipment_list) }}</textarea>
                                @error('equipment_list')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Information Display -->
                        <div class="alert alert-light">
                            <strong>Vehicle Record Information:</strong>
                            <ul class="mb-0">
                                <li>Added to Fleet: {{ $vehicle->created_at->format('F d, Y \a\t g:i A') }}</li>
                                @if($vehicle->updated_at && $vehicle->updated_at != $vehicle->created_at)
                                    <li>Last Updated: {{ $vehicle->updated_at->format('F d, Y \a\t g:i A') }}</li>
                                @endif
                                <li>Current Status:
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
                                    @endswitch
                                </li>
                            </ul>
                        </div>

                        <!-- Form Actions -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Vehicle Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-calculate next maintenance date when last maintenance is set
    $('#last_maintenance').on('change', function() {
        const lastDate = new Date($(this).val());
        const nextMaintenanceField = $('#next_maintenance_due');

        // Only auto-set if field is empty and the date is in the future
        if (lastDate && !nextMaintenanceField.val() && lastDate > new Date()) {
            // Add 3 months (90 days) as default
            const nextDate = new Date(lastDate);
            nextDate.setDate(nextDate.getDate() + 90);
            nextMaintenanceField.val(nextDate.toISOString().split('T')[0]);
        }
    });

    // Fuel level visual feedback
    $('#current_fuel').on('input', function() {
        const fuelLevel = parseFloat($(this).val());
        const fuelCapacity = parseFloat($('#fuel_capacity').val()) || 100;
        const fuelPercent = (fuelLevel / fuelCapacity) * 100;
        const $this = $(this);

        // Remove existing classes
        $this.removeClass('border-success border-warning border-danger');

        // Add appropriate class based on fuel percentage
        if (fuelPercent >= 50) {
            $this.addClass('border-success');
        } else if (fuelPercent >= 25) {
            $this.addClass('border-warning');
        } else {
            $this.addClass('border-danger');
        }
    });

    // Trigger fuel level check on page load
    $('#current_fuel').trigger('input');

    // Status change warnings
    $('#status').on('change', function() {
        const status = $(this).val();
        if (status === 'out_of_service') {
            if (!confirm('Are you sure you want to mark this vehicle as "Out of Service"? This will make it unavailable for all incidents.')) {
                $(this).val('{{ $vehicle->status }}'); // Revert to original
            }
        }
    });

    // Maintenance date validation - only warn for newly set past dates
    $('#next_maintenance_due').on('change', function() {
        const nextDate = new Date($(this).val());
        const today = new Date();
        const originalValue = '{{ $vehicle->next_maintenance_due?->format('Y-m-d') }}';

        // Only warn if user actively sets a past date (not just editing existing past dates)
        if (nextDate < today && $(this).val() !== originalValue) {
            if (!confirm('The selected maintenance date is in the past. This vehicle may need immediate attention. Continue?')) {
                $(this).val(originalValue); // Revert to original
            }
        }
    });
});
</script>
@endpush
