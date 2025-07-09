<!-- Schedule Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Schedule Maintenance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="maintenanceForm">
                <div class="modal-body">
                    <input type="hidden" id="maintenanceVehicleId">

                    <div class="mb-3">
                        <label for="maintenanceDate" class="form-label">Maintenance Date</label>
                        <input type="date" class="form-control" id="maintenanceDate"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        <div class="form-text">Select when the maintenance should be scheduled</div>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Scheduling maintenance will automatically change the vehicle status to "Maintenance" and make it unavailable for deployment.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-check me-2"></i>Schedule Maintenance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Maintenance scheduling form
    document.getElementById('maintenanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const vehicleId = document.getElementById('maintenanceVehicleId').value;
        const maintenanceDate = document.getElementById('maintenanceDate').value;

        fetch(`/vehicles/${vehicleId}/schedule-maintenance`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ maintenance_date: maintenanceDate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.status-badge-${vehicleId}`).className = `status-badge-${vehicleId} ${data.vehicle.status_badge}`;
                document.querySelector(`.status-badge-${vehicleId}`).textContent = 'Maintenance';

                bootstrap.Modal.getInstance(document.getElementById('maintenanceModal')).hide();
                showToast('success', data.message);

                // Refresh page to update maintenance due information
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            showToast('error', 'An error occurred while scheduling maintenance');
            console.error('Error:', error);
        });
    });
});
</script>
