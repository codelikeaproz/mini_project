<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class IncidentController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level in web.php
    }

    /**
     * Display a listing of incidents
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['incident_type', 'status', 'municipality', 'start_date', 'end_date', 'severity_level']);

        $query = Incident::with(['reportedBy', 'assignedStaff', 'assignedVehicle']);

        // Apply filters
        if (!empty($filters['incident_type'])) {
            $query->where('incident_type', $filters['incident_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['municipality'])) {
            $query->where('municipality', $filters['municipality']);
        }

        if (!empty($filters['severity_level'])) {
            $query->where('severity_level', $filters['severity_level']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('incident_datetime', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('incident_datetime', '<=', $filters['end_date']);
        }

        $incidents = $query->orderBy('incident_datetime', 'desc')->paginate(15);

        // Get statistics for the dashboard cards
        $stats = [
            'total' => Incident::count(),
            'pending' => Incident::where('status', 'pending')->count(),
            'responding' => Incident::where('status', 'responding')->count(),
            'resolved' => Incident::where('status', 'resolved')->count(),
            'closed' => Incident::where('status', 'closed')->count(),
        ];

        // Get staff and vehicles for assignment modal
        $staff = User::where('role', 'mdrrmo_staff')->orWhere('role', 'admin')->get();
        $vehicles = Vehicle::available()->get();

        return view('incidents.index', [
            'incidents' => $incidents,
            'filters' => $filters,
            'stats' => $stats,
            'incidentTypes' => Incident::INCIDENT_TYPES,
            'statusOptions' => Incident::STATUS_OPTIONS,
            'severityLevels' => Incident::SEVERITY_LEVELS,
            'staff' => $staff,
            'vehicles' => $vehicles,
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
        $validated = $request->validate([
            'incident_type' => 'required|in:' . implode(',', array_keys(Incident::INCIDENT_TYPES)),
            'location' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'incident_datetime' => 'required|date',
            'description' => 'required|string',
            'severity_level' => 'required|in:' . implode(',', array_keys(Incident::SEVERITY_LEVELS)),
            'vehicles_involved' => 'nullable|integer|min:0',
            'casualties_count' => 'nullable|integer|min:0',
            'injuries_count' => 'nullable|integer|min:0',
            'estimated_damage' => 'nullable|numeric|min:0',
            'hospital_destination' => 'nullable|string|max:255',
            'patient_condition' => 'nullable|in:' . implode(',', array_keys(Incident::PATIENT_CONDITIONS)),
            'medical_notes' => 'nullable|string',
            'weather_condition' => 'nullable|in:' . implode(',', array_keys(Incident::WEATHER_CONDITIONS)),
            'road_condition' => 'nullable|in:' . implode(',', array_keys(Incident::ROAD_CONDITIONS)),
            'assigned_staff' => 'nullable|exists:users,id',
            'assigned_vehicle' => 'nullable|exists:vehicles,id',
        ]);

        try {
            DB::beginTransaction();

            // Generate incident number and add defaults
            $validated['incident_number'] = Incident::generateIncidentNumber();
            $validated['reported_by'] = Auth::id();
            $validated['municipality'] = 'Maramag';
            $validated['status'] = 'pending';

            $incident = Incident::create($validated);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_created',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Created incident {$incident->incident_number}",
                'new_values' => $validated,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()
                ->route('incidents.show', $incident)
                ->with('success', "Incident {$incident->incident_number} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
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
        $incident = Incident::with(['reportedBy', 'assignedStaff', 'assignedVehicle', 'victims'])
            ->findOrFail($id);

        // Get staff and vehicles for assignment dropdowns
        $staff = User::where('role', 'mdrrmo_staff')->orWhere('role', 'admin')->get();
        $vehicles = Vehicle::available()->get();

        return view('incidents.show', compact('incident', 'staff', 'vehicles'));
    }

    /**
     * Show the form for editing the specified incident
     */
    public function edit(int $id): View
    {
        $incident = Incident::findOrFail($id);
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
        $incident = Incident::findOrFail($id);

        $validated = $request->validate([
            'incident_type' => 'required|in:' . implode(',', array_keys(Incident::INCIDENT_TYPES)),
            'location' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'incident_datetime' => 'required|date',
            'description' => 'required|string',
            'severity_level' => 'required|in:' . implode(',', array_keys(Incident::SEVERITY_LEVELS)),
            'status' => 'required|in:' . implode(',', array_keys(Incident::STATUS_OPTIONS)),
            'vehicles_involved' => 'nullable|integer|min:0',
            'casualties_count' => 'nullable|integer|min:0',
            'injuries_count' => 'nullable|integer|min:0',
            'estimated_damage' => 'nullable|numeric|min:0',
            'hospital_destination' => 'nullable|string|max:255',
            'patient_condition' => 'nullable|in:' . implode(',', array_keys(Incident::PATIENT_CONDITIONS)),
            'medical_notes' => 'nullable|string',
            'weather_condition' => 'nullable|in:' . implode(',', array_keys(Incident::WEATHER_CONDITIONS)),
            'road_condition' => 'nullable|in:' . implode(',', array_keys(Incident::ROAD_CONDITIONS)),
            'assigned_staff' => 'nullable|exists:users,id',
            'assigned_vehicle' => 'nullable|exists:vehicles,id',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $incident->toArray();
            $incident->update($validated);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_updated',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Updated incident {$incident->incident_number}",
                'old_values' => $oldValues,
                'new_values' => $validated,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()
                ->route('incidents.show', $incident)
                ->with('success', "Incident {$incident->incident_number} updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update incident: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified incident from storage
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $incident = Incident::findOrFail($id);
            $incidentNumber = $incident->incident_number;

            DB::beginTransaction();

            // Log activity before deletion
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_deleted',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Deleted incident {$incidentNumber}",
                'old_values' => $incident->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $incident->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Incident {$incidentNumber} deleted successfully!"
                ]);
            }

            return redirect()
                ->route('incidents.index')
                ->with('success', "Incident {$incidentNumber} deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            // Return JSON error response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete incident: ' . $e->getMessage()
                ], 422);
            }

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
            $request->validate([
                'status' => 'required|in:' . implode(',', array_keys(Incident::STATUS_OPTIONS))
            ]);

            $incident = Incident::findOrFail($id);
            $oldStatus = $incident->status;

            DB::beginTransaction();

            $incident->update(['status' => $request->status]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_status_updated',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Updated incident {$incident->incident_number} status from {$oldStatus} to {$request->status}",
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $request->status],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Incident status updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
            $request->validate([
                'staff_id' => 'required|exists:users,id'
            ]);

            $incident = Incident::findOrFail($id);
            $staff = User::findOrFail($request->staff_id);

            DB::beginTransaction();

            $incident->update(['assigned_staff' => $request->staff_id]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_staff_assigned',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Assigned {$staff->first_name} {$staff->last_name} to incident {$incident->incident_number}",
                'new_values' => ['assigned_staff' => $request->staff_id],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Staff assigned successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id'
            ]);

            $incident = Incident::findOrFail($id);
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            DB::beginTransaction();

            $incident->update(['assigned_vehicle' => $request->vehicle_id]);

            // Update vehicle status to deployed
            $vehicle->update(['status' => 'deployed']);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_vehicle_assigned',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Assigned vehicle {$vehicle->vehicle_number} to incident {$incident->incident_number}",
                'new_values' => ['assigned_vehicle' => $request->vehicle_id],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle assigned successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign vehicle: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Assign both staff and vehicle to incident in one call
     */
    public function assign(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'staff_id' => 'required|exists:users,id',
                'vehicle_id' => 'nullable|exists:vehicles,id'
            ]);

            $incident = Incident::findOrFail($id);
            $staff = User::findOrFail($request->staff_id);
            $vehicle = $request->vehicle_id ? Vehicle::findOrFail($request->vehicle_id) : null;

            DB::beginTransaction();

            // Update incident assignments
            $updateData = ['assigned_staff' => $request->staff_id];
            if ($request->vehicle_id) {
                $updateData['assigned_vehicle'] = $request->vehicle_id;
            }
            $incident->update($updateData);

            // Update vehicle status if assigned
            if ($vehicle) {
                $vehicle->update(['status' => 'deployed']);
            }

            // Log activity
            $description = "Assigned {$staff->first_name} {$staff->last_name}";
            if ($vehicle) {
                $description .= " and vehicle {$vehicle->vehicle_number}";
            }
            $description .= " to incident {$incident->incident_number}";

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_assigned',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => $description,
                'new_values' => $updateData,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assignment completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update a specific field of an incident (AJAX)
     */
    public function updateField(Request $request, int $id): JsonResponse
    {
        try {
            $field = $request->input('field');
            $value = $request->input('value');

            // Validate allowed fields
            $allowedFields = ['status', 'assigned_staff', 'assigned_vehicle'];
            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Field not allowed for update'
                ], 400);
            }

            $incident = Incident::findOrFail($id);

            DB::beginTransaction();

            // Special handling for vehicle assignment
            if ($field === 'assigned_vehicle') {
                // If removing vehicle assignment
                if (empty($value)) {
                    if ($incident->assigned_vehicle) {
                        $vehicle = Vehicle::find($incident->assigned_vehicle);
                        if ($vehicle) {
                            $vehicle->update(['status' => 'available']);
                        }
                    }
                    $incident->update(['assigned_vehicle' => null]);
                } else {
                    // Validate vehicle exists
                    $vehicle = Vehicle::findOrFail($value);

                    // Release previous vehicle if assigned
                    if ($incident->assigned_vehicle && $incident->assigned_vehicle != $value) {
                        $prevVehicle = Vehicle::find($incident->assigned_vehicle);
                        if ($prevVehicle) {
                            $prevVehicle->update(['status' => 'available']);
                        }
                    }

                    $incident->update(['assigned_vehicle' => $value]);
                    $vehicle->update(['status' => 'deployed']);
                }
            } else {
                // For status and staff assignment
                $incident->update([$field => $value ?: null]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'incident_field_updated',
                'model_type' => 'App\Models\Incident',
                'model_id' => $incident->id,
                'description' => "Updated {$field} for incident {$incident->incident_number}",
                'new_values' => [$field => $value],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
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
            $perPage = $request->input('per_page', 15);

            $query = Incident::with(['reportedBy', 'assignedStaff', 'assignedVehicle']);

            // Apply filters (same logic as index method)
            if (!empty($filters['incident_type'])) {
                $query->where('incident_type', $filters['incident_type']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['municipality'])) {
                $query->where('municipality', $filters['municipality']);
            }

            if (!empty($filters['start_date'])) {
                $query->whereDate('incident_datetime', '>=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $query->whereDate('incident_datetime', '<=', $filters['end_date']);
            }

            $incidents = $query->orderBy('incident_datetime', 'desc')->paginate($perPage);

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
            $stats = [
                'total' => Incident::count(),
                'pending' => Incident::where('status', 'pending')->count(),
                'responding' => Incident::where('status', 'responding')->count(),
                'resolved' => Incident::where('status', 'resolved')->count(),
                'closed' => Incident::where('status', 'closed')->count(),
                'this_month' => Incident::whereMonth('incident_datetime', now()->month)
                    ->whereYear('incident_datetime', now()->year)
                    ->count(),
                'this_week' => Incident::whereBetween('incident_datetime', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'today' => Incident::whereDate('incident_datetime', now())->count(),
                'by_severity' => [
                    'minor' => Incident::where('severity_level', 'minor')->count(),
                    'moderate' => Incident::where('severity_level', 'moderate')->count(),
                    'severe' => Incident::where('severity_level', 'severe')->count(),
                    'critical' => Incident::where('severity_level', 'critical')->count(),
                ],
                'by_type' => Incident::selectRaw('incident_type, COUNT(*) as count')
                    ->groupBy('incident_type')
                    ->pluck('count', 'incident_type')
                    ->toArray(),
            ];

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

            $query = Incident::whereNotNull('latitude')
                ->whereNotNull('longitude');

            // Apply filters
            if (!empty($filters['incident_type'])) {
                $query->where('incident_type', $filters['incident_type']);
            }

            if (!empty($filters['start_date'])) {
                $query->whereDate('incident_datetime', '>=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $query->whereDate('incident_datetime', '<=', $filters['end_date']);
            }

            $incidents = $query->get();

            return response()->json([
                'success' => true,
                'data' => $incidents->map(function ($incident) {
                    return [
                        'id' => $incident->id,
                        'lat' => (float) $incident->latitude,
                        'lng' => (float) $incident->longitude,
                        'type' => $incident->incident_type,
                        'severity' => $incident->severity_level,
                        'date' => $incident->incident_datetime->format('Y-m-d H:i:s'),
                        'location' => $incident->location,
                        'description' => $incident->description,
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
            $data = [];
            $startDate = now()->subMonths(11)->startOfMonth();

            for ($i = 0; $i < 12; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $count = Incident::whereYear('incident_datetime', $month->year)
                    ->whereMonth('incident_datetime', $month->month)
                    ->count();

                $data[] = [
                    'month' => $month->format('M Y'),
                    'count' => $count
                ];
            }

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
            $data = Incident::selectRaw('incident_type, COUNT(*) as count')
                ->groupBy('incident_type')
                ->orderBy('count', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->incident_type,
                        'label' => Incident::INCIDENT_TYPES[$item->incident_type] ?? $item->incident_type,
                        'count' => $item->count
                    ];
                })
                ->toArray();

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
