<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Incident;
use App\Models\User;
use App\Repositories\IncidentRepository;
use App\DTOs\BaseDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class IncidentService extends BaseService
{
    public function __construct(
        private readonly IncidentRepository $incidentRepository
    ) {
        parent::__construct($incidentRepository);
    }

    /**
     * Create a new incident
     */
    public function createIncident(array $data): Incident
    {
        return $this->executeWithTransaction(function () use ($data) {
            // Validate user role
            $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

            // Generate incident number
            $data['incident_number'] = Incident::generateIncidentNumber();

            // Set reporter to current user
            $data['reported_by'] = Auth::id();

            // Validate data
            $validatedData = $this->validateInput($data, 'incident');

            // Create incident
            $incident = $this->incidentRepository->create($validatedData);

            // Log activity
            $this->logActivity(
                'incident_created',
                "Created incident {$incident->incident_number}",
                'Incident',
                $incident->id,
                null,
                $incident->toArray()
            );

            return $incident;
        });
    }

    /**
     * Update an incident
     */
    public function updateIncident(int $id, array $data): Incident
    {
        return $this->executeWithTransaction(function () use ($id, $data) {
            // Validate user role
            $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

            // Get current incident
            $incident = $this->incidentRepository->findOrFail($id);
            $oldData = $incident->toArray();

            // Validate data
            $validatedData = $this->validateInput($data, 'incident');

            // Update incident
            $updatedIncident = $this->incidentRepository->update($id, $validatedData);

            // Log activity
            $this->logActivity(
                'incident_updated',
                "Updated incident {$incident->incident_number}",
                'Incident',
                $id,
                $oldData,
                $validatedData
            );

            return $updatedIncident;
        });
    }

    /**
     * Delete an incident
     */
    public function deleteIncident(int $id): bool
    {
        return $this->executeWithTransaction(function () use ($id) {
            // Validate admin role for deletion
            $this->ensureRoleAllowed(['admin']);

            // Get incident for logging
            $incident = $this->incidentRepository->findOrFail($id);
            $incidentData = $incident->toArray();

            // Delete incident
            $deleted = $this->incidentRepository->delete($id);

            if ($deleted) {
                // Log activity
                $this->logActivity(
                    'incident_deleted',
                    "Deleted incident {$incident->incident_number}",
                    'Incident',
                    $id,
                    $incidentData,
                    null
                );
            }

            return $deleted;
        });
    }

    /**
     * Get paginated incidents with filters
     */
    public function getPaginatedIncidents(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        // Sanitize filters
        $filters = $this->sanitizeInput($filters);

        return $this->incidentRepository->getPaginatedWithFilters($filters, $perPage);
    }

    /**
     * Get incident by ID with relations
     */
    public function getIncidentById(int $id): Incident
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->findOrFail($id, ['reporter', 'assignedStaff', 'assignedVehicle', 'victims']);
    }

    /**
     * Get incident by incident number
     */
    public function getIncidentByNumber(string $incidentNumber): ?Incident
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->findByIncidentNumber($incidentNumber);
    }

    /**
     * Get recent incidents
     */
    public function getRecentIncidents(int $limit = 10): Collection
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->getRecent($limit);
    }

    /**
     * Get pending incidents
     */
    public function getPendingIncidents(): Collection
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->getPending();
    }

    /**
     * Update incident status
     */
    public function updateIncidentStatus(int $id, string $status): bool
    {
        return $this->executeWithTransaction(function () use ($id, $status) {
            // Validate user role
            $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

            // Validate status
            $validStatuses = ['pending', 'responding', 'resolved', 'closed'];
            if (!in_array($status, $validStatuses)) {
                throw new \InvalidArgumentException("Invalid status: {$status}");
            }

            // Get incident for logging
            $incident = $this->incidentRepository->findOrFail($id);
            $oldStatus = $incident->status;

            // Update status
            $updated = $this->incidentRepository->updateStatus($id, $status);

            if ($updated) {
                // Log activity
                $this->logActivity(
                    'incident_status_updated',
                    "Updated incident {$incident->incident_number} status from {$oldStatus} to {$status}",
                    'Incident',
                    $id,
                    ['status' => $oldStatus],
                    ['status' => $status]
                );
            }

            return $updated;
        });
    }

    /**
     * Assign staff to incident
     */
    public function assignStaffToIncident(int $incidentId, int $staffId): bool
    {
        return $this->executeWithTransaction(function () use ($incidentId, $staffId) {
            // Validate admin role for assignment
            $this->ensureRoleAllowed(['admin']);

            // Validate staff exists and has correct role
            $staff = User::findOrFail($staffId);
            if (!in_array($staff->role, ['admin', 'mdrrmo_staff'])) {
                throw new \InvalidArgumentException('User is not MDRRMO staff');
            }

            // Get incident for logging
            $incident = $this->incidentRepository->findOrFail($incidentId);
            $oldStaffId = $incident->assigned_staff;

            // Assign staff
            $assigned = $this->incidentRepository->assignStaff($incidentId, $staffId);

            if ($assigned) {
                // Log activity
                $this->logActivity(
                    'incident_staff_assigned',
                    "Assigned {$staff->first_name} {$staff->last_name} to incident {$incident->incident_number}",
                    'Incident',
                    $incidentId,
                    ['assigned_staff' => $oldStaffId],
                    ['assigned_staff' => $staffId]
                );
            }

            return $assigned;
        });
    }

    /**
     * Assign vehicle to incident
     */
    public function assignVehicleToIncident(int $incidentId, int $vehicleId): bool
    {
        return $this->executeWithTransaction(function () use ($incidentId, $vehicleId) {
            // Validate user role
            $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

            // Get incident for logging
            $incident = $this->incidentRepository->findOrFail($incidentId);
            $oldVehicleId = $incident->assigned_vehicle;

            // Assign vehicle
            $assigned = $this->incidentRepository->assignVehicle($incidentId, $vehicleId);

            if ($assigned) {
                // Log activity
                $this->logActivity(
                    'incident_vehicle_assigned',
                    "Assigned vehicle to incident {$incident->incident_number}",
                    'Incident',
                    $incidentId,
                    ['assigned_vehicle' => $oldVehicleId],
                    ['assigned_vehicle' => $vehicleId]
                );
            }

            return $assigned;
        });
    }

    /**
     * Get incidents statistics
     */
    public function getIncidentStatistics(): array
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->getStatistics();
    }

    /**
     * Get incidents for heat map
     */
    public function getIncidentsForHeatMap(array $filters = []): Collection
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        // Sanitize filters
        $filters = $this->sanitizeInput($filters);

        return $this->incidentRepository->getForHeatMap($filters);
    }

    /**
     * Get monthly incident data for charts
     */
    public function getMonthlyIncidentData(int $months = 12): Collection
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->getMonthlyData($months);
    }

    /**
     * Get incident type distribution
     */
    public function getIncidentTypeDistribution(): Collection
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->getTypeDistribution();
    }

    /**
     * Get incidents by severity
     */
    public function getIncidentsBySeverity(): Collection
    {
        // Ensure user has access
        $this->ensureRoleAllowed(['admin', 'mdrrmo_staff']);

        return $this->incidentRepository->getBySeverity();
    }

    /**
     * Validate incident data
     */
    private function validateInput(array $data, string $type): array
    {
        if ($type === 'incident') {
            $rules = $this->getIncidentValidationRules();
            return $this->validateData($data, $rules);
        }

        throw new \InvalidArgumentException("Unknown validation type: {$type}");
    }
}
