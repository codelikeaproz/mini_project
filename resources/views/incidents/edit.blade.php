@extends('layouts.app')

@section('title', 'Edit Incident #' . $incident->incident_number . ' - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Edit Incident #{{ $incident->incident_number }}</h1>
            <p class="page-subtitle">Update emergency incident report for Maramag, Bukidnon</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-eye me-2"></i>View Details
                </a>
                <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container">
    <form action="{{ route('incidents.update', $incident) }}" method="POST" id="incidentForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="incident_type" class="form-label">Incident Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('incident_type') is-invalid @enderror"
                                        id="incident_type" name="incident_type" required onchange="updateFormFields()">
                                    <option value="">Select Incident Type</option>
                                    <optgroup label="Vehicle-Related Incidents">
                                        <option value="vehicle_vs_vehicle" {{ old('incident_type', $incident->incident_type) == 'vehicle_vs_vehicle' ? 'selected' : '' }}>Vehicle vs Vehicle</option>
                                        <option value="vehicle_vs_pedestrian" {{ old('incident_type', $incident->incident_type) == 'vehicle_vs_pedestrian' ? 'selected' : '' }}>Vehicle vs Pedestrian</option>
                                        <option value="vehicle_vs_animals" {{ old('incident_type', $incident->incident_type) == 'vehicle_vs_animals' ? 'selected' : '' }}>Vehicle vs Animals</option>
                                        <option value="vehicle_vs_property" {{ old('incident_type', $incident->incident_type) == 'vehicle_vs_property' ? 'selected' : '' }}>Vehicle vs Property</option>
                                        <option value="vehicle_alone" {{ old('incident_type', $incident->incident_type) == 'vehicle_alone' ? 'selected' : '' }}>Vehicle Alone</option>
                                    </optgroup>
                                    <optgroup label="Medical Emergency Incidents">
                                        <option value="maternity" {{ old('incident_type', $incident->incident_type) == 'maternity' ? 'selected' : '' }}>Maternity</option>
                                        <option value="stabbing_shooting" {{ old('incident_type', $incident->incident_type) == 'stabbing_shooting' ? 'selected' : '' }}>Stabbing/Shooting</option>
                                        <option value="transport_to_hospital" {{ old('incident_type', $incident->incident_type) == 'transport_to_hospital' ? 'selected' : '' }}>Transport to Hospital</option>
                                    </optgroup>
                                </select>
                                @error('incident_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="severity_level" class="form-label">Severity Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity_level') is-invalid @enderror"
                                        id="severity_level" name="severity_level" required>
                                    <option value="">Select Severity</option>
                                    <option value="minor" {{ old('severity_level', $incident->severity_level) == 'minor' ? 'selected' : '' }}>Minor</option>
                                    <option value="moderate" {{ old('severity_level', $incident->severity_level) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                    <option value="severe" {{ old('severity_level', $incident->severity_level) == 'severe' ? 'selected' : '' }}>Severe</option>
                                    <option value="critical" {{ old('severity_level', $incident->severity_level) == 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                                @error('severity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="incident_datetime" class="form-label">Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('incident_datetime') is-invalid @enderror"
                                       id="incident_datetime" name="incident_datetime"
                                       value="{{ old('incident_datetime', $incident->incident_datetime->format('Y-m-d\TH:i')) }}" required>
                                @error('incident_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('barangay') is-invalid @enderror"
                                       id="barangay" name="barangay" value="{{ old('barangay', $incident->barangay) }}"
                                       placeholder="e.g., Poblacion" required>
                                @error('barangay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="location" class="form-label">Specific Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location', $incident->location) }}"
                                       placeholder="e.g., Purok 1, near public market" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude (Optional)</label>
                                <input type="number" step="0.00000001" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude', $incident->latitude) }}"
                                       placeholder="e.g., 7.1247">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude (Optional)</label>
                                <input type="number" step="0.00000001" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude', $incident->longitude) }}"
                                       placeholder="e.g., 125.0623">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">For heat map visualization</small>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" required>{{ old('description', $incident->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle-Specific Fields -->
                <div class="card mb-4" id="vehicleFields">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-car me-2"></i>Vehicle Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="vehicles_involved" class="form-label">Number of Vehicles Involved</label>
                                <input type="number" class="form-control @error('vehicles_involved') is-invalid @enderror"
                                       id="vehicles_involved" name="vehicles_involved"
                                       value="{{ old('vehicles_involved', $incident->vehicles_involved) }}" min="0" max="10">
                                @error('vehicles_involved')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="estimated_damage" class="form-label">Estimated Damage (PHP)</label>
                                <input type="number" step="0.01" class="form-control @error('estimated_damage') is-invalid @enderror"
                                       id="estimated_damage" name="estimated_damage"
                                       value="{{ old('estimated_damage', $incident->estimated_damage) }}" min="0">
                                @error('estimated_damage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="weather_condition" class="form-label">Weather Condition</label>
                                <select class="form-select @error('weather_condition') is-invalid @enderror"
                                        id="weather_condition" name="weather_condition">
                                    <option value="">Not specified</option>
                                    <option value="clear" {{ old('weather_condition', $incident->weather_condition) == 'clear' ? 'selected' : '' }}>Clear</option>
                                    <option value="rainy" {{ old('weather_condition', $incident->weather_condition) == 'rainy' ? 'selected' : '' }}>Rainy</option>
                                    <option value="foggy" {{ old('weather_condition', $incident->weather_condition) == 'foggy' ? 'selected' : '' }}>Foggy</option>
                                    <option value="windy" {{ old('weather_condition', $incident->weather_condition) == 'windy' ? 'selected' : '' }}>Windy</option>
                                    <option value="stormy" {{ old('weather_condition', $incident->weather_condition) == 'stormy' ? 'selected' : '' }}>Stormy</option>
                                </select>
                                @error('weather_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="road_condition" class="form-label">Road Condition</label>
                                <select class="form-select @error('road_condition') is-invalid @enderror"
                                        id="road_condition" name="road_condition">
                                    <option value="">Not specified</option>
                                    <option value="dry" {{ old('road_condition', $incident->road_condition) == 'dry' ? 'selected' : '' }}>Dry</option>
                                    <option value="wet" {{ old('road_condition', $incident->road_condition) == 'wet' ? 'selected' : '' }}>Wet</option>
                                    <option value="slippery" {{ old('road_condition', $incident->road_condition) == 'slippery' ? 'selected' : '' }}>Slippery</option>
                                    <option value="under_construction" {{ old('road_condition', $incident->road_condition) == 'under_construction' ? 'selected' : '' }}>Under Construction</option>
                                    <option value="damaged" {{ old('road_condition', $incident->road_condition) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                </select>
                                @error('road_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical-Specific Fields -->
                <div class="card mb-4" id="medicalFields">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Medical Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="hospital_destination" class="form-label">Hospital Destination</label>
                                <input type="text" class="form-control @error('hospital_destination') is-invalid @enderror"
                                       id="hospital_destination" name="hospital_destination"
                                       value="{{ old('hospital_destination', $incident->hospital_destination) }}"
                                       placeholder="e.g., Bukidnon Provincial Hospital">
                                @error('hospital_destination')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="patient_condition" class="form-label">Patient Condition</label>
                                <select class="form-select @error('patient_condition') is-invalid @enderror"
                                        id="patient_condition" name="patient_condition">
                                    <option value="">Not specified</option>
                                    <option value="stable" {{ old('patient_condition', $incident->patient_condition) == 'stable' ? 'selected' : '' }}>Stable</option>
                                    <option value="critical" {{ old('patient_condition', $incident->patient_condition) == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="deceased" {{ old('patient_condition', $incident->patient_condition) == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                </select>
                                @error('patient_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="medical_notes" class="form-label">Medical Notes</label>
                                <textarea class="form-control @error('medical_notes') is-invalid @enderror"
                                          id="medical_notes" name="medical_notes" rows="3">{{ old('medical_notes', $incident->medical_notes) }}</textarea>
                                @error('medical_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Casualties Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Casualties Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="casualties_count" class="form-label">Number of Casualties</label>
                                <input type="number" class="form-control @error('casualties_count') is-invalid @enderror"
                                       id="casualties_count" name="casualties_count"
                                       value="{{ old('casualties_count', $incident->casualties_count) }}" min="0" max="100">
                                @error('casualties_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="injuries_count" class="form-label">Number of Injuries</label>
                                <input type="number" class="form-control @error('injuries_count') is-invalid @enderror"
                                       id="injuries_count" name="injuries_count"
                                       value="{{ old('injuries_count', $incident->injuries_count) }}" min="0" max="100">
                                @error('injuries_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Update Incident
                            </button>
                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>

                        <hr>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong><br>
                            Changes will be logged for audit purposes. Make sure all information is accurate before saving.
                        </div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info me-2"></i>Current Status</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> <span class="badge status-{{ $incident->status }}">{{ ucfirst($incident->status) }}</span></p>
                        <p><strong>Created:</strong> {{ $incident->created_at->format('M d, Y h:i A') }}</p>
                        <p><strong>Last Updated:</strong> {{ $incident->updated_at->format('M d, Y h:i A') }}</p>
                        @if($incident->assignedStaff)
                            <p><strong>Assigned to:</strong> {{ $incident->assignedStaff->full_name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateFormFields() {
    const incidentType = document.getElementById('incident_type').value;
    const vehicleFields = document.getElementById('vehicleFields');
    const medicalFields = document.getElementById('medicalFields');

    // Show/hide fields based on incident type
    if (incidentType.includes('vehicle_')) {
        vehicleFields.style.display = 'block';
        medicalFields.style.display = 'none';
    } else if (['maternity', 'stabbing_shooting', 'transport_to_hospital'].includes(incidentType)) {
        vehicleFields.style.display = 'none';
        medicalFields.style.display = 'block';
    } else {
        vehicleFields.style.display = 'none';
        medicalFields.style.display = 'none';
    }
}

// Initialize form fields on page load
document.addEventListener('DOMContentLoaded', function() {
    updateFormFields();
});

// Form validation
document.getElementById('incidentForm').addEventListener('submit', function(e) {
    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    submitBtn.disabled = true;

    // Re-enable button after 5 seconds as fallback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});
</script>
@endpush
