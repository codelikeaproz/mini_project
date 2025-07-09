<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Vehicle;
use App\Repositories\VehicleRepository;
use App\Services\BaseService;
use App\DTOs\VehicleDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

final class VehicleService extends BaseService
{
    public function __construct(
        private readonly VehicleRepository $vehicleRepository
    ) {
        parent::__construct();
    }

    /**
     * Get all vehicles with pagination and filtering
     */
    public function getVehicles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $this->logActivity('vehicles.list', 'Accessed vehicle list with filters', [
                'filters' => $filters,
                'per_page' => $perPage
            ]);

            return $this->vehicleRepository->getFiltered($filters, $perPage);
        } catch (Exception $e) {
            $this->logActivity('vehicles.list.error', 'Failed to access vehicle list', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get vehicle by ID
     */
    public function getVehicle(int $id): Vehicle
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicle = $this->vehicleRepository->findOrFail($id);

            $this->logActivity('vehicles.view', "Viewed vehicle: {$vehicle->vehicle_number}", [
                'vehicle_id' => $id,
                'vehicle_number' => $vehicle->vehicle_number
            ]);

            return $vehicle;
        } catch (Exception $e) {
            $this->logActivity('vehicles.view.error', 'Failed to view vehicle', [
                'vehicle_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create new vehicle
     */
    public function createVehicle(array $data): Vehicle
    {
        try {
            $this->validateRole(['admin']);

            // Validate and sanitize input
            $validatedData = $this->validateInput($data, [
                'vehicle_number' => 'required|string|max:50|unique:vehicles',
                'vehicle_type' => 'required|in:ambulance,fire_truck,rescue_vehicle,patrol_car,motorcycle,emergency_van',
                'make_model' => 'required|string|max:100',
                'year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
                'plate_number' => 'required|string|max:20|unique:vehicles',
                'capacity' => 'required|integer|min:1',
                'fuel_capacity' => 'required|numeric|min:0',
                'current_fuel' => 'nullable|numeric|min:0',
                'odometer_reading' => 'nullable|integer|min:0',
                'equipment_list' => 'nullable|string',
                'municipality' => 'nullable|string|max:100'
            ]);

            // Set defaults
            $validatedData['municipality'] = $validatedData['municipality'] ?? 'Maramag';
            $validatedData['current_fuel'] = $validatedData['current_fuel'] ?? 0;
            $validatedData['odometer_reading'] = $validatedData['odometer_reading'] ?? 0;
            $validatedData['status'] = 'available';
            $validatedData['is_operational'] = true;

            return DB::transaction(function () use ($validatedData) {
                $vehicle = $this->vehicleRepository->create($validatedData);

                $this->logActivity('vehicles.create', "Created new vehicle: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $vehicle->id,
                    'vehicle_data' => $validatedData
                ]);

                return $vehicle;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.create.error', 'Failed to create vehicle', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update vehicle
     */
    public function updateVehicle(int $id, array $data): Vehicle
    {
        try {
            $this->validateRole(['admin']);

            $vehicle = $this->vehicleRepository->findOrFail($id);
            $oldData = $vehicle->toArray();

            // Validate and sanitize input
            $validatedData = $this->validateInput($data, [
                'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number,' . $id,
                'vehicle_type' => 'required|in:ambulance,fire_truck,rescue_vehicle,patrol_car,motorcycle,emergency_van',
                'make_model' => 'required|string|max:100',
                'year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
                'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $id,
                'capacity' => 'required|integer|min:1',
                'fuel_capacity' => 'required|numeric|min:0',
                'current_fuel' => 'nullable|numeric|min:0',
                'odometer_reading' => 'nullable|integer|min:0',
                'equipment_list' => 'nullable|string',
                'municipality' => 'nullable|string|max:100',
                'status' => 'nullable|in:available,deployed,maintenance,out_of_service',
                'is_operational' => 'nullable|boolean'
            ]);

            return DB::transaction(function () use ($id, $validatedData, $vehicle, $oldData) {
                $updatedVehicle = $this->vehicleRepository->update($id, $validatedData);

                $this->logActivity('vehicles.update', "Updated vehicle: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $id,
                    'old_data' => $oldData,
                    'new_data' => $validatedData
                ]);

                return $updatedVehicle;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.update.error', 'Failed to update vehicle', [
                'vehicle_id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete vehicle
     */
    public function deleteVehicle(int $id): bool
    {
        try {
            $this->validateRole(['admin']);

            $vehicle = $this->vehicleRepository->findOrFail($id);

            // Check if vehicle is currently deployed
            if ($vehicle->status === 'deployed') {
                throw new Exception('Cannot delete vehicle that is currently deployed.');
            }

            return DB::transaction(function () use ($id, $vehicle) {
                $result = $this->vehicleRepository->delete($id);

                $this->logActivity('vehicles.delete', "Deleted vehicle: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $id,
                    'vehicle_data' => $vehicle->toArray()
                ]);

                return $result;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.delete.error', 'Failed to delete vehicle', [
                'vehicle_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get available vehicles for assignment
     */
    public function getAvailableVehicles(): Collection
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicles = $this->vehicleRepository->getAvailable();

            $this->logActivity('vehicles.available', 'Retrieved available vehicles list');

            return $vehicles;
        } catch (Exception $e) {
            $this->logActivity('vehicles.available.error', 'Failed to retrieve available vehicles', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update vehicle status
     */
    public function updateStatus(int $id, string $status): Vehicle
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicle = $this->vehicleRepository->findOrFail($id);
            $oldStatus = $vehicle->status;

            return DB::transaction(function () use ($id, $status, $vehicle, $oldStatus) {
                $updatedVehicle = $this->vehicleRepository->updateStatus($id, $status);

                $this->logActivity('vehicles.status_update', "Updated vehicle status: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $id,
                    'old_status' => $oldStatus,
                    'new_status' => $status
                ]);

                return $updatedVehicle;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.status_update.error', 'Failed to update vehicle status', [
                'vehicle_id' => $id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update fuel level
     */
    public function updateFuel(int $id, float $fuelLevel): Vehicle
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicle = $this->vehicleRepository->findOrFail($id);

            if ($fuelLevel > $vehicle->fuel_capacity) {
                throw new Exception('Fuel level cannot exceed vehicle fuel capacity.');
            }

            $oldFuel = $vehicle->current_fuel;

            return DB::transaction(function () use ($id, $fuelLevel, $vehicle, $oldFuel) {
                $updatedVehicle = $this->vehicleRepository->updateFuel($id, $fuelLevel);

                $this->logActivity('vehicles.fuel_update', "Updated fuel level: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $id,
                    'old_fuel' => $oldFuel,
                    'new_fuel' => $fuelLevel,
                    'fuel_percentage' => round(($fuelLevel / $vehicle->fuel_capacity) * 100, 2)
                ]);

                return $updatedVehicle;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.fuel_update.error', 'Failed to update fuel level', [
                'vehicle_id' => $id,
                'fuel_level' => $fuelLevel,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Schedule maintenance
     */
    public function scheduleMaintenance(int $id, Carbon $maintenanceDate): Vehicle
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicle = $this->vehicleRepository->findOrFail($id);

            return DB::transaction(function () use ($id, $maintenanceDate, $vehicle) {
                $updatedVehicle = $this->vehicleRepository->scheduleMaintenance($id, $maintenanceDate);

                $this->logActivity('vehicles.maintenance_schedule', "Scheduled maintenance: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $id,
                    'maintenance_date' => $maintenanceDate->toDateString()
                ]);

                return $updatedVehicle;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.maintenance_schedule.error', 'Failed to schedule maintenance', [
                'vehicle_id' => $id,
                'maintenance_date' => $maintenanceDate->toDateString(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Complete maintenance
     */
    public function completeMaintenance(int $id, array $maintenanceData = []): Vehicle
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicle = $this->vehicleRepository->findOrFail($id);

            return DB::transaction(function () use ($id, $maintenanceData, $vehicle) {
                $updatedVehicle = $this->vehicleRepository->completeMaintenance($id, $maintenanceData);

                $this->logActivity('vehicles.maintenance_complete', "Completed maintenance: {$vehicle->vehicle_number}", [
                    'vehicle_id' => $id,
                    'maintenance_data' => $maintenanceData
                ]);

                return $updatedVehicle;
            });
        } catch (Exception $e) {
            $this->logActivity('vehicles.maintenance_complete.error', 'Failed to complete maintenance', [
                'vehicle_id' => $id,
                'maintenance_data' => $maintenanceData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get vehicle statistics
     */
    public function getStatistics(): array
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $stats = $this->vehicleRepository->getStatistics();

            $this->logActivity('vehicles.statistics', 'Retrieved vehicle statistics');

            return $stats;
        } catch (Exception $e) {
            $this->logActivity('vehicles.statistics.error', 'Failed to retrieve vehicle statistics', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get vehicles needing attention (low fuel, maintenance due)
     */
    public function getVehiclesNeedingAttention(): array
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $needingMaintenance = $this->vehicleRepository->getNeedingMaintenance();
            $lowFuel = $this->vehicleRepository->getLowFuel();

            $this->logActivity('vehicles.attention', 'Retrieved vehicles needing attention');

            return [
                'maintenance' => $needingMaintenance,
                'low_fuel' => $lowFuel
            ];
        } catch (Exception $e) {
            $this->logActivity('vehicles.attention.error', 'Failed to retrieve vehicles needing attention', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get deployment history for a vehicle
     */
    public function getDeploymentHistory(int $id): Collection
    {
        try {
            $this->validateRole(['admin', 'mdrrmo_staff']);

            $vehicle = $this->vehicleRepository->findOrFail($id);
            $history = $this->vehicleRepository->getDeploymentHistory($id);

            $this->logActivity('vehicles.deployment_history', "Retrieved deployment history: {$vehicle->vehicle_number}", [
                'vehicle_id' => $id
            ]);

            return $history;
        } catch (Exception $e) {
            $this->logActivity('vehicles.deployment_history.error', 'Failed to retrieve deployment history', [
                'vehicle_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
