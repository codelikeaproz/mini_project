@extends('layouts.app')

@section('title', 'Victim Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Victim Management</h1>
                <div class="d-sm-flex">
                    <a href="{{ route('victims.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Victim
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- DataTables Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Victims</h6>
                </div>
                <div class="card-body">
                    @if($victims->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Incident</th>
                                        <th>Injury Status</th>
                                        <th>Medical Attention</th>
                                        <th>Date Recorded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($victims as $victim)
                                        <tr>
                                            <td>
                                                <strong>{{ $victim->name }}</strong>
                                                @if($victim->contact_number)
                                                    <br><small class="text-muted">{{ $victim->contact_number }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $victim->age }} years</td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ ucfirst($victim->gender) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($victim->incident)
                                                    <a href="{{ route('incidents.show', $victim->incident->id) }}" class="text-primary">
                                                        <strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $victim->incident->incident_type)) }}</strong>
                                                        <br><small class="text-muted">{{ $victim->incident->location }}</small>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Incident Deleted</span>
                                                @endif
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                @if($victim->medical_attention_required)
                                                    <span class="badge badge-danger">Required</span>
                                                    @if($victim->hospital_name)
                                                        <br><small class="text-muted">{{ $victim->hospital_name }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Not Required</span>
                                                @endif
                                            </td>
                                            <td>{{ $victim->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('victims.show', $victim->id) }}"
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('victims.edit', $victim->id) }}"
                                                       class="btn btn-sm btn-warning" title="Edit Victim">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete({{ $victim->id }})" title="Delete Victim">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $victims->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No victims recorded yet.</p>
                            <a href="{{ route('victims.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Victim
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete functionality now handled by SweetAlert2 -->
@endsection

@push('scripts')
<script>
function confirmDelete(victimId) {
    // Get victim name from the table row
    const victimRow = document.querySelector(`button[onclick="confirmDelete(${victimId})"]`).closest('tr');
    const victimName = victimRow.querySelector('td:first-child').textContent.trim();

    showDeleteConfirmation(
        'Delete Victim Record',
        'Are you sure you want to delete this victim record?',
        victimName,
        'Yes, Delete Record',
        function() {
            showLoading('Deleting victim record...');

            fetch(`/victims/${victimId}`, {
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

// Initialize DataTable if there are records
@if($victims->count() > 0)
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 6, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });
});
@endif
</script>
@endpush
