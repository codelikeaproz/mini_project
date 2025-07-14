@extends('layouts.app')

@section('title', 'Add New Victim')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Add New Victim</h1>
                <a href="{{ route('victims.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Victims
                </a>
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
                    <h6 class="m-0 font-weight-bold text-primary">Victim Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('victims.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Incident Selection -->
                            <div class="col-md-12 mb-4">
                                <label for="incident_id" class="form-label">
                                    <strong>Related Incident <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('incident_id') is-invalid @enderror"
                                        id="incident_id" name="incident_id" required>
                                    <option value="">Select Incident...</option>
                                    @foreach($incidents as $incident)
                                        <option value="{{ $incident->id }}"
                                                {{ old('incident_id', $incident?->id) == $incident->id ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $incident->incident_type)) }} -
                                            {{ $incident->location }}
                                            ({{ $incident->created_at->format('M d, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('incident_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <strong>Full Name <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="age" class="form-label">
                                    <strong>Age <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number"
                                       class="form-control @error('age') is-invalid @enderror"
                                       id="age" name="age"
                                       value="{{ old('age') }}"
                                       min="0" max="150" required>
                                @error('age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="gender" class="form-label">
                                    <strong>Gender <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('gender') is-invalid @enderror"
                                        id="gender" name="gender" required>
                                    <option value="">Select Gender...</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">
                                    <strong>Contact Number</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('contact_number') is-invalid @enderror"
                                       id="contact_number" name="contact_number"
                                       value="{{ old('contact_number') }}"
                                       placeholder="e.g., 09123456789">
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">
                                    <strong>Address <span class="text-danger">*</span></strong>
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Medical Information -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Medical Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="injury_status" class="form-label">
                                    <strong>Injury Status <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('injury_status') is-invalid @enderror"
                                        id="injury_status" name="injury_status" required>
                                    <option value="">Select Injury Status...</option>
                                    <option value="uninjured" {{ old('injury_status') == 'uninjured' ? 'selected' : '' }}>Uninjured</option>
                                    <option value="minor_injury" {{ old('injury_status') == 'minor_injury' ? 'selected' : '' }}>Minor Injury</option>
                                    <option value="major_injury" {{ old('injury_status') == 'major_injury' ? 'selected' : '' }}>Major Injury</option>
                                    <option value="critical" {{ old('injury_status') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="deceased" {{ old('injury_status') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                </select>
                                @error('injury_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="medical_attention_required" class="form-label">
                                    <strong>Medical Attention Required <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control @error('medical_attention_required') is-invalid @enderror"
                                        id="medical_attention_required" name="medical_attention_required" required>
                                    <option value="">Select...</option>
                                    <option value="1" {{ old('medical_attention_required') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('medical_attention_required') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('medical_attention_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="hospital_name" class="form-label">
                                    <strong>Hospital/Medical Facility</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('hospital_name') is-invalid @enderror"
                                       id="hospital_name" name="hospital_name"
                                       value="{{ old('hospital_name') }}"
                                       placeholder="Name of hospital or medical facility">
                                @error('hospital_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Emergency Contact</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_name" class="form-label">
                                    <strong>Emergency Contact Name</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                       id="emergency_contact_name" name="emergency_contact_name"
                                       value="{{ old('emergency_contact_name') }}">
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_number" class="form-label">
                                    <strong>Emergency Contact Number</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('emergency_contact_number') is-invalid @enderror"
                                       id="emergency_contact_number" name="emergency_contact_number"
                                       value="{{ old('emergency_contact_number') }}"
                                       placeholder="e.g., 09123456789">
                                @error('emergency_contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">
                                    <strong>Additional Notes</strong>
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3"
                                          placeholder="Any additional information about the victim or medical condition">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('victims.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Victim Information
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
    // Auto-populate incident if coming from incident page
    @if(isset($incident) && $incident)
        $('#incident_id').val('{{ $incident->id }}').change();
    @endif

    // Show/hide hospital field based on medical attention requirement
    $('#medical_attention_required').change(function() {
        const hospitalField = $('#hospital_name').closest('.row');
        if ($(this).val() == '1') {
            hospitalField.show();
            $('#hospital_name').attr('required', true);
        } else {
            hospitalField.hide();
            $('#hospital_name').attr('required', false);
        }
    });

    // Trigger change event on page load
    $('#medical_attention_required').change();
});
</script>
@endpush
