<!-- Fuel Update Modal -->
<div class="modal fade" id="fuelModal" tabindex="-1" aria-labelledby="fuelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fuelModalLabel">
                    <i class="fas fa-gas-pump me-2"></i>Update Fuel Level
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fuelForm">
                <div class="modal-body">
                    <input type="hidden" id="fuelVehicleId">

                    <div class="mb-3">
                        <label for="newFuelLevel" class="form-label">Fuel Level (Liters)</label>
                        <input type="number" class="form-control" id="newFuelLevel"
                               step="0.1" min="0" max="200" required
                               placeholder="Enter fuel level in liters">
                        <div class="form-text">Enter the current fuel level in liters</div>
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Make sure to update fuel levels after refueling or after missions to maintain accurate records.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Fuel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
