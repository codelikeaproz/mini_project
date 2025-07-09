<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Incident;
use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class IncidentRepository extends BaseRepository implements RepositoryInterface
{
    public function __construct(Incident $model)
    {
        parent::__construct($model);
    }

    /**
     * Get incidents with relationships
     */
    public function getWithRelations(array $relations = ['reporter', 'assignedStaff', 'assignedVehicle', 'victims']): Collection
    {
        return $this->query()
            ->with($relations)
            ->orderBy('incident_datetime', 'desc')
            ->get();
    }

    /**
     * Get paginated incidents with filters
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()->with(['reporter', 'assignedStaff', 'assignedVehicle']);

        // Apply filters
        if (!empty($filters['incident_type'])) {
            $query->byType($filters['incident_type']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['municipality'])) {
            $query->byMunicipality($filters['municipality']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['severity_level'])) {
            $query->where('severity_level', $filters['severity_level']);
        }

        return $query->orderBy('incident_datetime', 'desc')->paginate($perPage);
    }

    /**
     * Get incidents by type
     */
    public function getByType(string $type): Collection
    {
        return $this->query()
            ->byType($type)
            ->with(['reporter', 'assignedStaff', 'victims'])
            ->orderBy('incident_datetime', 'desc')
            ->get();
    }

    /**
     * Get recent incidents
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->query()
            ->with(['reporter', 'assignedStaff', 'assignedVehicle'])
            ->orderBy('incident_datetime', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending incidents
     */
    public function getPending(): Collection
    {
        return $this->query()
            ->byStatus('pending')
            ->with(['reporter', 'assignedStaff', 'victims'])
            ->orderBy('incident_datetime', 'desc')
            ->get();
    }

    /**
     * Get incidents for heat map (with coordinates)
     */
    public function getForHeatMap(array $filters = []): Collection
    {
        $query = $this->query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(['id', 'incident_type', 'latitude', 'longitude', 'incident_datetime', 'severity_level']);

        // Apply filters
        if (!empty($filters['incident_type'])) {
            $query->byType($filters['incident_type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        return $query->get();
    }

    /**
     * Get incidents statistics
     */
    public function getStatistics(): array
    {
        $total = $this->count();
        $pending = $this->query()->byStatus('pending')->count();
        $responding = $this->query()->byStatus('responding')->count();
        $resolved = $this->query()->byStatus('resolved')->count();
        $closed = $this->query()->byStatus('closed')->count();

        $vehicleIncidents = $this->query()->whereIn('incident_type', [
            'vehicle_vs_vehicle', 'vehicle_vs_pedestrian', 'vehicle_vs_animals',
            'vehicle_vs_property', 'vehicle_alone'
        ])->count();

        $medicalIncidents = $this->query()->whereIn('incident_type', [
            'maternity', 'stabbing_shooting', 'transport_to_hospital'
        ])->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'responding' => $responding,
            'resolved' => $resolved,
            'closed' => $closed,
            'vehicle_related' => $vehicleIncidents,
            'medical_emergency' => $medicalIncidents,
            'today' => $this->query()->whereDate('incident_datetime', today())->count(),
            'this_week' => $this->query()->whereBetween('incident_datetime', [
                now()->startOfWeek(), now()->endOfWeek()
            ])->count(),
            'this_month' => $this->query()->whereMonth('incident_datetime', now()->month)->count(),
        ];
    }

    /**
     * Get incidents by month for charts
     */
    public function getMonthlyData(int $months = 12): Collection
    {
        return $this->query()
            ->selectRaw('EXTRACT(YEAR FROM incident_datetime) as year')
            ->selectRaw('EXTRACT(MONTH FROM incident_datetime) as month')
            ->selectRaw('COUNT(*) as count')
            ->where('incident_datetime', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get incident type distribution
     */
    public function getTypeDistribution(): Collection
    {
        return $this->query()
            ->selectRaw('incident_type, COUNT(*) as count')
            ->groupBy('incident_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get incidents by severity
     */
    public function getBySeverity(): Collection
    {
        return $this->query()
            ->selectRaw('severity_level, COUNT(*) as count')
            ->groupBy('severity_level')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Find by incident number
     */
    public function findByIncidentNumber(string $incidentNumber): ?Incident
    {
        return $this->query()
            ->where('incident_number', $incidentNumber)
            ->with(['reporter', 'assignedStaff', 'assignedVehicle', 'victims'])
            ->first();
    }

    /**
     * Update incident status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Assign staff to incident
     */
    public function assignStaff(int $incidentId, int $staffId): bool
    {
        return $this->update($incidentId, ['assigned_staff' => $staffId]);
    }

    /**
     * Assign vehicle to incident
     */
    public function assignVehicle(int $incidentId, int $vehicleId): bool
    {
        return $this->update($incidentId, ['assigned_vehicle' => $vehicleId]);
    }
}
