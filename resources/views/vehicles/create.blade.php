@extends('layouts.app')

@section('title', 'Add New Vehicle - MDRRMO')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add New Vehicle</h1>
            <p class="text-muted">Register a new vehicle to the MDRRMO fleet</p>
        </div>
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Fleet
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Vehicle Registration Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vehicle Registration</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('vehicles.store') }}" method="POST" id="vehicleForm">
                        @csrf

                        <!-- Vehicle Identification -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Vehicle Identification</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="vehicle_number" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror"
                                       id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}"
                                       placeholder="e.g., MDR-007" required>
                                @error('vehicle_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: MDR-### (MDRRMO vehicle numbering)</div>
                            </div>

                            <div class="col-md-6">
                                <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('plate_number') is-invalid @enderror"
                                       id="plate_number" name="plate_number" value="{{ old('plate_number') }}"
                                       placeholder="e.g., ABC-1234" required>
                                @error('plate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Vehicle Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Vehicle Details</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="vehicle_type" class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('vehicle_type') is-invalid @enderror"
                                        id="vehicle_type" name="vehicle_type" required>
                                    <option value="">Select Vehicle Type</option>
                                    <option value="ambulance" {{ old('vehicle_type') === 'ambulance' ? 'selected' : '' }}>Ambulance</option>
                                    <option value="fire_truck" {{ old('vehicle_type') === 'fire_truck' ? 'selected' : '' }}>Fire Truck</option>
                                    <option value="rescue_vehicle" {{ old('vehicle_type') === 'rescue_vehicle' ? 'selected' : '' }}>Rescue Vehicle</option>
                                    <option value="patrol_car" {{ old('vehicle_type') === 'patrol_car' ? 'selected' : '' }}>Patrol Car</option>
                                    <option value="motorcycle" {{ old('vehicle_type') === 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                    <option value="emergency_van" {{ old('vehicle_type') === 'emergency_van' ? 'selected' : '' }}>Emergency Van</option>
                                </select>
                                @error('vehicle_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="make_model" class="form-label">Make & Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('make_model') is-invalid @enderror"
                                       id="make_model" name="make_model" value="{{ old('make_model') }}"
                                       placeholder="e.g., Toyota Hiace, Ford F-150" required>
                                @error('make_model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror"
                                       id="year" name="year" value="{{ old('year') }}"
                                       min="1950" max="{{ date('Y') + 1 }}" required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="municipality" class="form-label">Municipality</label>
                                <input type="text" class="form-control @error('municipality') is-invalid @enderror"
                                       id="municipality" name="municipality" value="{{ old('municipality', 'Maramag') }}"
                                       readonly>
                                @error('municipality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">MDRRMO vehicles are registered in Maramag</div>
                            </div>
                        </div>

                        <!-- Capacity and Specifications -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Capacity & Specifications</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="capacity" class="form-label">Passenger Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                       id="capacity" name="capacity" value="{{ old('capacity') }}"
                                       min="1" max="50" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Number of people the vehicle can carry</div>
                            </div>

                            <div class="col-md-6">
                                <label for="fuel_capacity" class="form-label">Fuel Capacity (Liters) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('fuel_capacity') is-invalid @enderror"
                                       id="fuel_capacity" name="fuel_capacity" value="{{ old('fuel_capacity') }}"
                                       step="0.1" min="5" max="200" required>
                                @error('fuel_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="current_fuel" class="form-label">Current Fuel Level (Liters)</label>
                                <input type="number" class="form-control @error('current_fuel') is-invalid @enderror"
                                       id="current_fuel" name="current_fuel" value="{{ old('current_fuel', '0') }}"
                                       step="0.1" min="0">
                                @error('current_fuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Leave as 0 if empty, update after first refuel</div>
                            </div>

                            <div class="col-md-6">
                                <label for="odometer_reading" class="form-label">Current Odometer Reading (km)</label>
                                <input type="number" class="form-control @error('odometer_reading') is-invalid @enderror"
                                       id="odometer_reading" name="odometer_reading" value="{{ old('odometer_reading', '0') }}"
                                       min="0">
                                @error('odometer_reading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Equipment & Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Equipment & Additional Information</h6>
                            </div>

                            <div class="col-12">
                                <label for="equipment_list" class="form-label">Equipment List</label>
                                <textarea class="form-control @error('equipment_list') is-invalid @enderror"
                                          id="equipment_list" name="equipment_list" rows="4"
                                          placeholder="List emergency equipment, medical supplies, tools, etc.">{{ old('equipment_list') }}</textarea>
                                @error('equipment_list')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">List all emergency equipment and supplies carried by this vehicle</div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Register Vehicle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Vehicle Type Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Vehicle Type Information</h6>
                </div>
                <div class="card-body">
                    <div class="vehicle-type-info" id="vehicleTypeInfo">
                        <p class="text-muted">Select a vehicle type to see typical specifications and equipment.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Registration Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Use MDR-### format for vehicle numbers
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Enter accurate fuel capacity for proper tracking
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            List all emergency equipment for deployment planning
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Vehicle will be set as "Available" and "Operational" by default
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleTypeSelect = document.getElementById('vehicle_type');
    const vehicleTypeInfo = document.getElementById('vehicleTypeInfo');

    const typeInformation = {
        ambulance: {
            title: 'Ambulance',
            description: 'Emergency medical transport vehicle',
            typical_equipment: [
                'Medical supplies and first aid kit',
                'Stretcher and patient monitoring equipment',
                'Oxygen tanks and ventilation equipment',
                'Defibrillator and emergency medications',
                'Communication radio system'
            ],
            typical_capacity: '2-4 persons (plus patient)',
            typical_fuel: '60-80 liters'
        },
        fire_truck: {
            title: 'Fire Truck',
            description: 'Fire suppression and rescue vehicle',
            typical_equipment: [
                'Water tanks and hoses',
                'Fire suppression foam',
                'Ladders and rescue equipment',
                'Cutting and breaking tools',
                'Protective gear and breathing apparatus'
            ],
            typical_capacity: '4-6 persons',
            typical_fuel: '150-200 liters'
        },
        rescue_vehicle: {
            title: 'Rescue Vehicle',
            description: 'Search and rescue operations',
            typical_equipment: [
                'Rescue ropes and climbing gear',
                'Cutting and lifting tools',
                'Communication equipment',
                'Emergency lighting',
                'First aid supplies'
            ],
            typical_capacity: '4-8 persons',
            typical_fuel: '80-120 liters'
        },
        patrol_car: {
            title: 'Patrol Car',
            description: 'Security and rapid response',
            typical_equipment: [
                'Communication radio',
                'Emergency lights and sirens',
                'Basic first aid kit',
                'Traffic control equipment',
                'Emergency tools'
            ],
            typical_capacity: '2-4 persons',
            typical_fuel: '50-70 liters'
        },
        motorcycle: {
            title: 'Motorcycle',
            description: 'Quick response and reconnaissance',
            typical_equipment: [
                'Communication radio',
                'Basic first aid kit',
                'Emergency tools',
                'Safety equipment'
            ],
            typical_capacity: '1-2 persons',
            typical_fuel: '15-25 liters'
        },
        emergency_van: {
            title: 'Emergency Van',
            description: 'Multi-purpose emergency response',
            typical_equipment: [
                'Emergency supplies',
                'Communication equipment',
                'Basic rescue tools',
                'First aid supplies',
                'Emergency lighting'
            ],
            typical_capacity: '6-10 persons',
            typical_fuel: '60-90 liters'
        }
    };

    vehicleTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;

        if (selectedType && typeInformation[selectedType]) {
            const info = typeInformation[selectedType];
            vehicleTypeInfo.innerHTML = `
                <h6 class="text-info">${info.title}</h6>
                <p class="small text-muted mb-2">${info.description}</p>

                <div class="mb-3">
                    <strong class="small">Typical Capacity:</strong><br>
                    <span class="small">${info.typical_capacity}</span>
                </div>

                <div class="mb-3">
                    <strong class="small">Typical Fuel Capacity:</strong><br>
                    <span class="small">${info.typical_fuel}</span>
                </div>

                <div>
                    <strong class="small">Typical Equipment:</strong>
                    <ul class="small mb-0 mt-1">
                        ${info.typical_equipment.map(item => `<li>${item}</li>`).join('')}
                    </ul>
                </div>
            `;
        } else {
            vehicleTypeInfo.innerHTML = '<p class="text-muted">Select a vehicle type to see typical specifications and equipment.</p>';
        }
    });

    // Form validation enhancement
    const form = document.getElementById('vehicleForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering Vehicle...';
    });

    // Auto-suggest vehicle number based on type
    vehicleTypeSelect.addEventListener('change', function() {
        const vehicleNumberInput = document.getElementById('vehicle_number');
        if (!vehicleNumberInput.value) {
            // Suggest next available number (this is just a placeholder logic)
            const typeCode = this.value.charAt(0).toUpperCase();
            vehicleNumberInput.placeholder = `e.g., MDR-${typeCode}01`;
        }
    });
});
</script>
@endsection
