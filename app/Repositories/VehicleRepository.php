<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Vehicle;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

final class VehicleRepository implements RepositoryInterface
{
    public function __construct(
        private readonly Vehicle $model
    ) {}

    public function find(int $id): ?Vehicle
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Vehicle
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Vehicle
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Vehicle
    {
        $vehicle = $this->findOrFail($id);
        $vehicle->update($data);
        return $vehicle->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get vehicles with filtering and pagination
     */
    public function getFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Search by vehicle number, type, or plate number
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('vehicle_number', 'ilike', "%{$search}%")
                  ->orWhere('vehicle_type', 'ilike', "%{$search}%")
                  ->orWhere('plate_number', 'ilike', "%{$search}%")
                  ->orWhere('make_model', 'ilike', "%{$search}%");
            });
        }

        // Filter by vehicle type
        if (!empty($filters['type'])) {
            $query->where('vehicle_type', $filters['type']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by operational status
        if (isset($filters['operational'])) {
            $query->where('is_operational', $filters['operational']);
        }

        // Filter by municipality
        if (!empty($filters['municipality'])) {
            $query->where('municipality', $filters['municipality']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get vehicles by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get available vehicles for assignment
     */
    public function getAvailable(): Collection
    {
        return $this->model->available()->operational()->get();
    }

    /**
     * Get vehicles by type
     */
    public function getByType(string $type): Collection
    {
        return $this->model->where('vehicle_type', $type)->get();
    }

    /**
     * Get vehicle statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'available' => $this->model->where('status', 'available')->count(),
            'deployed' => $this->model->where('status', 'deployed')->count(),
            'maintenance' => $this->model->where('status', 'maintenance')->count(),
            'out_of_service' => $this->model->where('status', 'out_of_service')->count(),
            'operational' => $this->model->where('is_operational', true)->count(),
            'non_operational' => $this->model->where('is_operational', false)->count(),
        ];
    }

    /**
     * Get vehicle type distribution
     */
    public function getTypeDistribution(): array
    {
        return $this->model
            ->selectRaw('vehicle_type, COUNT(*) as count')
            ->groupBy('vehicle_type')
            ->pluck('count', 'vehicle_type')
            ->toArray();
    }

    /**
     * Get vehicles needing maintenance
     */
    public function getNeedingMaintenance(): Collection
    {
        $today = Carbon::today();

        return $this->model
            ->where(function (Builder $query) use ($today) {
                $query->where('next_maintenance_due', '<=', $today)
                      ->orWhere('next_maintenance_due', '<=', $today->addDays(7)); // Alert 7 days before
            })
            ->where('is_operational', true)
            ->orderBy('next_maintenance_due')
            ->get();
    }

    /**
     * Get vehicles with low fuel
     */
    public function getLowFuel(float $threshold = 25.0): Collection
    {
        return $this->model
            ->whereRaw('(current_fuel / fuel_capacity * 100) <= ?', [$threshold])
            ->where('is_operational', true)
            ->orderByRaw('(current_fuel / fuel_capacity * 100)')
            ->get();
    }

    /**
     * Get deployment history for a vehicle
     */
    public function getDeploymentHistory(int $vehicleId): Collection
    {
        return $this->model->findOrFail($vehicleId)
            ->incidents()
            ->with(['reportedBy', 'assignedStaff'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update vehicle fuel level
     */
    public function updateFuel(int $vehicleId, float $fuelLevel): Vehicle
    {
        $vehicle = $this->findOrFail($vehicleId);
        $vehicle->update(['current_fuel' => $fuelLevel]);
        return $vehicle->fresh();
    }

    /**
     * Update vehicle status
     */
    public function updateStatus(int $vehicleId, string $status): Vehicle
    {
        $vehicle = $this->findOrFail($vehicleId);
        $vehicle->update(['status' => $status]);
        return $vehicle->fresh();
    }

    /**
     * Update odometer reading
     */
    public function updateOdometer(int $vehicleId, int $reading): Vehicle
    {
        $vehicle = $this->findOrFail($vehicleId);
        $vehicle->update(['odometer_reading' => $reading]);
        return $vehicle->fresh();
    }

    /**
     * Schedule maintenance
     */
    public function scheduleMaintenance(int $vehicleId, Carbon $maintenanceDate): Vehicle
    {
        $vehicle = $this->findOrFail($vehicleId);
        $vehicle->update([
            'next_maintenance_due' => $maintenanceDate,
            'status' => 'maintenance'
        ]);
        return $vehicle->fresh();
    }

    /**
     * Complete maintenance
     */
    public function completeMaintenance(int $vehicleId, array $maintenanceData = []): Vehicle
    {
        $vehicle = $this->findOrFail($vehicleId);

        $updateData = [
            'last_maintenance' => Carbon::today(),
            'status' => 'available',
            'is_operational' => true
        ];

        // Add next maintenance date if provided (e.g., 3 months from now)
        if (isset($maintenanceData['next_maintenance_due'])) {
            $updateData['next_maintenance_due'] = $maintenanceData['next_maintenance_due'];
        } else {
            $updateData['next_maintenance_due'] = Carbon::today()->addMonths(3);
        }

        // Update odometer if provided
        if (isset($maintenanceData['odometer_reading'])) {
            $updateData['odometer_reading'] = $maintenanceData['odometer_reading'];
        }

        // Refuel if specified
        if (isset($maintenanceData['refuel']) && $maintenanceData['refuel']) {
            $updateData['current_fuel'] = $vehicle->fuel_capacity;
        }

        $vehicle->update($updateData);
        return $vehicle->fresh();
    }
}
