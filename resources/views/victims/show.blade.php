@extends('layouts.app')

@section('title', 'Victim Details - ' . $victim->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Victim Details</h1>
                    <p class="mb-0 text-gray-600">{{ $victim->full_name }}</p>
                </div>
                <div class="d-sm-flex">
                    <a href="{{ route('victims.edit', $victim->id) }}" class="btn btn-warning shadow-sm mr-2">
                        <i class="fas fa-edit fa-sm text-white-50"></i> Edit Victim
                    </a>
                    <a href="{{ route('victims.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
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
                <!-- Personal Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Full Name:</strong></div>
                                <div class="col-sm-8">{{ $victim->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Age:</strong></div>
                                <div class="col-sm-8">{{ $victim->age }} years old</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Gender:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge badge-secondary">{{ ucfirst($victim->gender) }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Contact:</strong></div>
                                <div class="col-sm-8">
                                    {{ $victim->contact_number ?: 'Not provided' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Address:</strong></div>
                                <div class="col-sm-8">{{ $victim->address }}</div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><strong>Recorded:</strong></div>
                                <div class="col-sm-8">{{ $victim->created_at->format('F d, Y \a\t g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Medical Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-5"><strong>Injury Status:</strong></div>
                                <div class="col-sm-7">
                                    @switch($victim->injury_status)
                                        @case('uninjured')
                                            <span class="badge badge-success">Uninjured</span>
                                            @break
                                        @case('minor_injury')
                                            <span class="badge badge-warning">Minor Injury</span>
                                            @break
                                        @case('major_injury')
                                            <span class="badge badge-danger">Major Injury</span>
                                            @break
                                        @case('critical')
                                            <span class="badge badge-dark">Critical</span>
                                            @break
                                        @case('deceased')
                                            <span class="badge badge-secondary">Deceased</span>
                                            @break
                                        @default
                                            <span class="badge badge-light">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $victim->injury_status)) }}</span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-5"><strong>Medical Attention:</strong></div>
                                <div class="col-sm-7">
                                    @if($victim->medical_attention_required)
                                        <span class="badge badge-danger">Required</span>
                                    @else
                                        <span class="badge badge-secondary">Not Required</span>
                                    @endif
                                </div>
                            </div>
                            @if($victim->hospital_name)
                                <div class="row mb-3">
                                    <div class="col-sm-5"><strong>Hospital/Facility:</strong></div>
                                    <div class="col-sm-7">{{ $victim->hospital_name }}</div>
                                </div>
                            @endif
                            @if($victim->notes)
                                <div class="row">
                                    <div class="col-sm-5"><strong>Medical Notes:</strong></div>
                                    <div class="col-sm-7">{{ $victim->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Incident Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Related Incident</h6>
                        </div>
                        <div class="card-body">
                            @if($victim->incident)
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Incident Type:</strong></div>
                                    <div class="col-sm-8">
                                        {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $victim->incident->incident_type)) }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Location:</strong></div>
                                    <div class="col-sm-8">{{ $victim->incident->location }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Date/Time:</strong></div>
                                    <div class="col-sm-8">{{ $victim->incident->incident_datetime ? $victim->incident->incident_datetime->format('F d, Y \a\t g:i A') : 'N/A' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Status:</strong></div>
                                    <div class="col-sm-8">
                                        @switch($victim->incident->status)
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
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4"><strong>View Incident:</strong></div>
                                    <div class="col-sm-8">
                                        <a href="{{ route('incidents.show', $victim->incident->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> View Full Incident
                                        </a>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Related incident has been deleted or is no longer available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Emergency Contact</h6>
                        </div>
                        <div class="card-body">
                            @if($victim->emergency_contact_name || $victim->emergency_contact_number)
                                @if($victim->emergency_contact_name)
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Name:</strong></div>
                                        <div class="col-sm-8">{{ $victim->emergency_contact_name }}</div>
                                    </div>
                                @endif
                                @if($victim->emergency_contact_number)
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Phone:</strong></div>
                                        <div class="col-sm-8">
                                            <a href="tel:{{ $victim->emergency_contact_number }}" class="text-primary">
                                                {{ $victim->emergency_contact_number }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">No emergency contact information provided.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('victims.edit', $victim->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Victim Information
                                    </a>
                                    @if($victim->incident)
                                        <a href="{{ route('incidents.show', $victim->incident->id) }}" class="btn btn-primary ml-2">
                                            <i class="fas fa-external-link-alt"></i> View Related Incident
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash"></i> Delete Victim Record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete functionality now handled by SweetAlert2 -->
@endsection

@push('scripts')
<script>
function confirmDelete() {
    showDeleteConfirmation(
        'Delete Victim Record',
        'Are you sure you want to delete this victim record?',
        '{{ $victim->full_name }}',
        'Yes, Delete Record',
        function() {
            showLoading('Deleting victim record...');

            fetch(`/victims/{{ $victim->id }}`, {
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
                    showSuccessToast(data.message || 'Victim record deleted successfully');
                    setTimeout(() => {
                        window.location.href = '/victims';
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
</script>
@endpush
