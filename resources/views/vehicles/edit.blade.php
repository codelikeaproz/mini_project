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
                                <label for="type" class="form-label">
                                    <strong>Vehicle Type <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Select Type...</option>
                                    <option value="ambulance" {{ old('type', $vehicle->type) == 'ambulance' ? 'selected' : '' }}>Ambulance</option>
                                    <option value="fire_truck" {{ old('type', $vehicle->type) == 'fire_truck' ? 'selected' : '' }}>Fire Truck</option>
                                    <option value="rescue_vehicle" {{ old('type', $vehicle->type) == 'rescue_vehicle' ? 'selected' : '' }}>Rescue Vehicle</option>
                                    <option value="patrol_car" {{ old('type', $vehicle->type) == 'patrol_car' ? 'selected' : '' }}>Patrol Car</option>
                                    <option value="utility_vehicle" {{ old('type', $vehicle->type) == 'utility_vehicle' ? 'selected' : '' }}>Utility Vehicle</option>
                                    <option value="motorcycle" {{ old('type', $vehicle->type) == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="model" class="form-label">
                                    <strong>Model</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('model') is-invalid @enderror"
                                       id="model" name="model"
                                       value="{{ old('model', $vehicle->model) }}"
                                       placeholder="e.g., Toyota Hiace, Isuzu NPR">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="year" class="form-label">
                                    <strong>Year</strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('year') is-invalid @enderror"
                                       id="year" name="year"
                                       value="{{ old('year', $vehicle->year) }}"
                                       min="1990" max="{{ date('Y') + 1 }}">
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="license_plate" class="form-label">
                                    <strong>License Plate</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('license_plate') is-invalid @enderror"
                                       id="license_plate" name="license_plate"
                                       value="{{ old('license_plate', $vehicle->license_plate) }}"
                                       placeholder="e.g., ABC-1234">
                                @error('license_plate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status and Assignment -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Status & Assignment</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <strong>Current Status <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status...</option>
                                    <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="in_use" {{ old('status', $vehicle->status) == 'in_use' ? 'selected' : '' }}>In Use</option>
                                    <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    <option value="out_of_service" {{ old('status', $vehicle->status) == 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="current_location" class="form-label">
                                    <strong>Current Location</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('current_location') is-invalid @enderror"
                                       id="current_location" name="current_location"
                                       value="{{ old('current_location', $vehicle->current_location) }}"
                                       placeholder="e.g., MDRRMO Station, Maramag">
                                @error('current_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="assigned_driver" class="form-label">
                                    <strong>Assigned Driver</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('assigned_driver') is-invalid @enderror"
                                       id="assigned_driver" name="assigned_driver"
                                       value="{{ old('assigned_driver', $vehicle->assigned_driver) }}"
                                       placeholder="Driver name or ID">
                                @error('assigned_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fuel Information -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Fuel Information</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fuel_level" class="form-label">
                                    <strong>Current Fuel Level (%) <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('fuel_level') is-invalid @enderror"
                                       id="fuel_level" name="fuel_level"
                                       value="{{ old('fuel_level', $vehicle->fuel_level) }}"
                                       min="0" max="100" required>
                                @error('fuel_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="mileage" class="form-label">
                                    <strong>Current Mileage (km)</strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('mileage') is-invalid @enderror"
                                       id="mileage" name="mileage"
                                       value="{{ old('mileage', $vehicle->mileage) }}"
                                       min="0" step="0.1">
                                @error('mileage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="last_refuel_amount" class="form-label">
                                    <strong>Last Refuel Amount (liters)</strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('last_refuel_amount') is-invalid @enderror"
                                       id="last_refuel_amount" name="last_refuel_amount"
                                       value="{{ old('last_refuel_amount', $vehicle->last_refuel_amount) }}"
                                       min="0" step="0.1">
                                @error('last_refuel_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_refuel_date" class="form-label">
                                    <strong>Last Refuel Date</strong>
                                </label>
                                <input type="date"
                                       class="form-control @error('last_refuel_date') is-invalid @enderror"
                                       id="last_refuel_date" name="last_refuel_date"
                                       value="{{ old('last_refuel_date', $vehicle->last_refuel_date?->format('Y-m-d')) }}">
                                @error('last_refuel_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Maintenance Information -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Maintenance Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_maintenance_date" class="form-label">
                                    <strong>Last Maintenance Date</strong>
                                </label>
                                <input type="date"
                                       class="form-control @error('last_maintenance_date') is-invalid @enderror"
                                       id="last_maintenance_date" name="last_maintenance_date"
                                       value="{{ old('last_maintenance_date', $vehicle->last_maintenance_date?->format('Y-m-d')) }}">
                                @error('last_maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="next_maintenance_date" class="form-label">
                                    <strong>Next Scheduled Maintenance</strong>
                                </label>
                                <input type="date"
                                       class="form-control @error('next_maintenance_date') is-invalid @enderror"
                                       id="next_maintenance_date" name="next_maintenance_date"
                                       value="{{ old('next_maintenance_date', $vehicle->next_maintenance_date?->format('Y-m-d')) }}">
                                @error('next_maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="maintenance_type" class="form-label">
                                    <strong>Maintenance Type</strong>
                                </label>
                                <select class="form-control @error('maintenance_type') is-invalid @enderror"
                                        id="maintenance_type" name="maintenance_type">
                                    <option value="">Select Type...</option>
                                    <option value="routine" {{ old('maintenance_type', $vehicle->maintenance_type) == 'routine' ? 'selected' : '' }}>Routine Maintenance</option>
                                    <option value="repair" {{ old('maintenance_type', $vehicle->maintenance_type) == 'repair' ? 'selected' : '' }}>Repair</option>
                                    <option value="inspection" {{ old('maintenance_type', $vehicle->maintenance_type) == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                    <option value="emergency" {{ old('maintenance_type', $vehicle->maintenance_type) == 'emergency' ? 'selected' : '' }}>Emergency Repair</option>
                                </select>
                                @error('maintenance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="maintenance_notes" class="form-label">
                                    <strong>Maintenance Notes</strong>
                                </label>
                                <textarea class="form-control @error('maintenance_notes') is-invalid @enderror"
                                          id="maintenance_notes" name="maintenance_notes" rows="2">{{ old('maintenance_notes', $vehicle->maintenance_notes) }}</textarea>
                                @error('maintenance_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Additional Information</h5>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">
                                    <strong>Vehicle Notes</strong>
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3"
                                          placeholder="Any additional information about the vehicle">{{ old('notes', $vehicle->notes) }}</textarea>
                                @error('notes')
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
    $('#last_maintenance_date').on('change', function() {
        const lastDate = new Date($(this).val());
        if (lastDate && !$('#next_maintenance_date').val()) {
            // Add 3 months (90 days) as default
            const nextDate = new Date(lastDate);
            nextDate.setDate(nextDate.getDate() + 90);
            $('#next_maintenance_date').val(nextDate.toISOString().split('T')[0]);
        }
    });

    // Fuel level visual feedback
    $('#fuel_level').on('input', function() {
        const fuelLevel = parseInt($(this).val());
        const $this = $(this);

        // Remove existing classes
        $this.removeClass('border-success border-warning border-danger');

        // Add appropriate class based on fuel level
        if (fuelLevel >= 50) {
            $this.addClass('border-success');
        } else if (fuelLevel >= 25) {
            $this.addClass('border-warning');
        } else {
            $this.addClass('border-danger');
        }
    });

    // Trigger fuel level check on page load
    $('#fuel_level').trigger('input');

    // Status change warnings
    $('#status').on('change', function() {
        const status = $(this).val();
        if (status === 'out_of_service') {
            if (!confirm('Are you sure you want to mark this vehicle as "Out of Service"? This will make it unavailable for all incidents.')) {
                $(this).val('{{ $vehicle->status }}'); // Revert to original
            }
        }
    });

    // Maintenance date validation
    $('#next_maintenance_date').on('change', function() {
        const nextDate = new Date($(this).val());
        const today = new Date();

        if (nextDate < today) {
            alert('Warning: The next maintenance date is in the past. This vehicle may need immediate attention.');
        }
    });
});
</script>
@endpush
