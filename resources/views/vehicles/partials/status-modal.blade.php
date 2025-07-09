<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Update Vehicle Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusVehicleId">

                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="available">Available</option>
                            <option value="deployed">Deployed</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="out_of_service">Out of Service</option>
                        </select>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <strong>Status Definitions:</strong><br>
                            <strong>Available:</strong> Ready for deployment<br>
                            <strong>Deployed:</strong> Currently on a mission<br>
                            <strong>Maintenance:</strong> Under maintenance or repair<br>
                            <strong>Out of Service:</strong> Not operational
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
