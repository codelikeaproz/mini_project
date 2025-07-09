<!-- Complete Maintenance Modal -->
<div class="modal fade" id="completeMaintenanceModal" tabindex="-1" aria-labelledby="completeMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeMaintenanceModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Complete Maintenance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="completeMaintenanceForm">
                <div class="modal-body">
                    <input type="hidden" id="completeMaintenanceVehicleId">

                    <div class="mb-3">
                        <label for="newOdometerReading" class="form-label">Odometer Reading (Optional)</label>
                        <input type="number" class="form-control" id="newOdometerReading"
                               min="0" placeholder="Enter current odometer reading">
                        <div class="form-text">Update the odometer reading if changed during maintenance</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="refuelVehicle">
                            <label class="form-check-label" for="refuelVehicle">
                                Refuel vehicle to full capacity
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nextMaintenanceDate" class="form-label">Next Maintenance Due (Optional)</label>
                        <input type="date" class="form-control" id="nextMaintenanceDate"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <div class="form-text">If not specified, will be set to 3 months from today</div>
                    </div>

                    <div class="alert alert-success">
                        <small>
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Completion:</strong> This will mark the maintenance as complete, set the vehicle status to "Available", and make it operational.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Complete Maintenance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Complete maintenance form
    document.getElementById('completeMaintenanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const vehicleId = document.getElementById('completeMaintenanceVehicleId').value;
        const odometerReading = document.getElementById('newOdometerReading').value;
        const refuel = document.getElementById('refuelVehicle').checked;
        const nextMaintenanceDate = document.getElementById('nextMaintenanceDate').value;

        const data = {
            refuel: refuel
        };

        if (odometerReading) {
            data.odometer_reading = parseInt(odometerReading);
        }

        if (nextMaintenanceDate) {
            data.next_maintenance_due = nextMaintenanceDate;
        }

        fetch(`/vehicles/${vehicleId}/complete-maintenance`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update status badge
                document.querySelector(`.status-badge-${vehicleId}`).className = `status-badge-${vehicleId} ${data.vehicle.status_badge}`;
                document.querySelector(`.status-badge-${vehicleId}`).textContent = 'Available';

                // Update fuel if refueled
                if (data.vehicle.fuel_percentage) {
                    const percentage = data.vehicle.fuel_percentage;
                    document.querySelector(`.fuel-percentage-${vehicleId}`).textContent = `${percentage.toFixed(1)}%`;
                    document.querySelector(`.fuel-percentage-${vehicleId}`).className = `fuel-percentage-${vehicleId} ${data.vehicle.fuel_status_class}`;
                    document.querySelector(`.fuel-bar-${vehicleId}`).style.width = `${percentage}%`;

                    // Update progress bar color
                    const progressBar = document.querySelector(`.fuel-bar-${vehicleId}`);
                    progressBar.className = progressBar.className.replace(/bg-(success|warning|danger)/, '');
                    if (percentage >= 75) progressBar.classList.add('bg-success');
                    else if (percentage >= 25) progressBar.classList.add('bg-warning');
                    else progressBar.classList.add('bg-danger');
                }

                bootstrap.Modal.getInstance(document.getElementById('completeMaintenanceModal')).hide();
                showToast('success', data.message);

                // Refresh page to update maintenance information
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            showToast('error', 'An error occurred while completing maintenance');
            console.error('Error:', error);
        });
    });
});
</script>
