<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\IncidentService;
use App\Models\Incident;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

final class IncidentController extends Controller
{
    public function __construct(
        private readonly IncidentService $incidentService
    ) {
        $this->middleware(['auth', 'verified', 'role:admin,mdrrmo_staff']);
    }

    /**
     * Display a listing of incidents
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['incident_type', 'status', 'municipality', 'start_date', 'end_date', 'severity_level']);
        $incidents = $this->incidentService->getPaginatedIncidents($filters, 15);

        return view('incidents.index', [
            'incidents' => $incidents,
            'filters' => $filters,
            'incidentTypes' => Incident::INCIDENT_TYPES,
            'statusOptions' => Incident::STATUS_OPTIONS,
            'severityLevels' => Incident::SEVERITY_LEVELS,
        ]);
    }

    /**
     * Show the form for creating a new incident
     */
    public function create(): View
    {
        $staff = User::where('role', 'mdrrmo_staff')->orWhere('role', 'admin')->get();
        $vehicles = Vehicle::available()->get();

        return view('incidents.create', [
            'incidentTypes' => Incident::INCIDENT_TYPES,
            'severityLevels' => Incident::SEVERITY_LEVELS,
            'statusOptions' => Incident::STATUS_OPTIONS,
            'patientConditions' => Incident::PATIENT_CONDITIONS,
            'weatherConditions' => Incident::WEATHER_CONDITIONS,
            'roadConditions' => Incident::ROAD_CONDITIONS,
            'staff' => $staff,
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Store a newly created incident in storage
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $incident = $this->incidentService->createIncident($request->all());

            return redirect()
                ->route('incidents.show', $incident)
                ->with('success', "Incident {$incident->incident_number} created successfully!");

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create incident: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified incident
     */
    public function show(int $id): View
    {
        $incident = $this->incidentService->getIncidentById($id);

        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified incident
     */
    public function edit(int $id): View
    {
        $incident = $this->incidentService->getIncidentById($id);
        $staff = User::where('role', 'mdrrmo_staff')->orWhere('role', 'admin')->get();
        $vehicles = Vehicle::available()->get();

        return view('incidents.edit', [
            'incident' => $incident,
            'incidentTypes' => Incident::INCIDENT_TYPES,
            'severityLevels' => Incident::SEVERITY_LEVELS,
            'statusOptions' => Incident::STATUS_OPTIONS,
            'patientConditions' => Incident::PATIENT_CONDITIONS,
            'weatherConditions' => Incident::WEATHER_CONDITIONS,
            'roadConditions' => Incident::ROAD_CONDITIONS,
            'staff' => $staff,
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Update the specified incident in storage
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $incident = $this->incidentService->updateIncident($id, $request->all());

            return redirect()
                ->route('incidents.show', $incident)
                ->with('success', "Incident {$incident->incident_number} updated successfully!");

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update incident: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified incident from storage
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $incident = $this->incidentService->getIncidentById($id);
            $incidentNumber = $incident->incident_number;

            $this->incidentService->deleteIncident($id);

            return redirect()
                ->route('incidents.index')
                ->with('success', "Incident {$incidentNumber} deleted successfully!");

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete incident: ' . $e->getMessage());
        }
    }

    /**
     * Update incident status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $status = $request->input('status');
            $this->incidentService->updateIncidentStatus($id, $status);

            return response()->json([
                'success' => true,
                'message' => 'Incident status updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Assign staff to incident
     */
    public function assignStaff(Request $request, int $id): JsonResponse
    {
        try {
            $staffId = $request->input('staff_id');
            $this->incidentService->assignStaffToIncident($id, $staffId);

            return response()->json([
                'success' => true,
                'message' => 'Staff assigned successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign staff: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Assign vehicle to incident
     */
    public function assignVehicle(Request $request, int $id): JsonResponse
    {
        try {
            $vehicleId = $request->input('vehicle_id');
            $this->incidentService->assignVehicleToIncident($id, $vehicleId);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle assigned successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign vehicle: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get incidents data for API
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['incident_type', 'status', 'municipality', 'start_date', 'end_date']);
            $incidents = $this->incidentService->getPaginatedIncidents($filters, $request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $incidents
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch incidents: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get incident statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->incidentService->getIncidentStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get incidents for heat map
     */
    public function heatMapData(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['incident_type', 'start_date', 'end_date']);
            $incidents = $this->incidentService->getIncidentsForHeatMap($filters);

            return response()->json([
                'success' => true,
                'data' => $incidents->map(function ($incident) {
                    return [
                        'lat' => (float) $incident->latitude,
                        'lng' => (float) $incident->longitude,
                        'type' => $incident->incident_type,
                        'severity' => $incident->severity_level,
                        'date' => $incident->incident_datetime->format('Y-m-d H:i:s'),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch heat map data: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get monthly chart data
     */
    public function monthlyData(): JsonResponse
    {
        try {
            $data = $this->incidentService->getMonthlyIncidentData(12);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch monthly data: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get incident type distribution
     */
    public function typeDistribution(): JsonResponse
    {
        try {
            $data = $this->incidentService->getIncidentTypeDistribution();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch type distribution: ' . $e->getMessage()
            ], 400);
        }
    }
}
