<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

final class VehicleController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level in web.php
    }

    /**
     * Display vehicle listing with filtering
     */
    public function index(Request $request): View
    {
        try {
            $query = Vehicle::query();

            // Apply filters
            if ($search = $request->get('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('make_model', 'like', "%{$search}%")
                      ->orWhere('plate_number', 'like', "%{$search}%");
                });
            }

            if ($type = $request->get('type')) {
                $query->where('vehicle_type', $type);
            }

            if ($status = $request->get('status')) {
                $query->where('status', $status);
            }

            if ($request->has('operational')) {
                $query->where('is_operational', $request->boolean('operational'));
            }

            if ($municipality = $request->get('municipality')) {
                $query->where('municipality', $municipality);
            }

            $vehicles = $query->orderBy('created_at', 'desc')->paginate(15);

            // Get statistics
            $statistics = [
                'total' => Vehicle::count(),
                'available' => Vehicle::where('status', 'available')->count(),
                'deployed' => Vehicle::where('status', 'deployed')->count(),
                'maintenance' => Vehicle::where('status', 'maintenance')->count(),
                'out_of_service' => Vehicle::where('status', 'out_of_service')->count(),
            ];

            // Get vehicles needing attention
            $attention = [
                'low_fuel' => Vehicle::whereRaw('(current_fuel / fuel_capacity) * 100 < 25')->get(),
                'maintenance' => Vehicle::where('next_maintenance_due', '<', now())->get(),
                'out_of_service' => Vehicle::where('status', 'out_of_service')->get(),
            ];

            // Provide default values for all filter keys to prevent undefined array key errors
            $filters = [
                'search' => $request->get('search', ''),
                'type' => $request->get('type', ''),
                'status' => $request->get('status', ''),
                'operational' => $request->get('operational'),
                'municipality' => $request->get('municipality', ''),
            ];

            return view('vehicles.index', compact('vehicles', 'statistics', 'attention', 'filters'));
        } catch (Exception $e) {
            // Show error view with empty data
            $vehicles = collect();
            $statistics = ['total' => 0, 'available' => 0, 'deployed' => 0, 'maintenance' => 0, 'out_of_service' => 0];
            $attention = ['low_fuel' => collect(), 'maintenance' => collect(), 'out_of_service' => collect()];
            $filters = [
                'search' => '',
                'type' => '',
                'status' => '',
                'operational' => null,
                'municipality' => '',
            ];

            return view('vehicles.index', compact('vehicles', 'statistics', 'attention', 'filters'))
                ->with('error', 'Error loading vehicles: ' . $e->getMessage());
        }
    }

    /**
     * Show form for creating new vehicle
     */
    public function create(): View
    {
        return view('vehicles.create', [
            'vehicleTypes' => Vehicle::VEHICLE_TYPES,
            'statusOptions' => Vehicle::STATUS_OPTIONS,
        ]);
    }

    /**
     * Store new vehicle
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:vehicles',
            'vehicle_type' => 'required|in:' . implode(',', array_keys(Vehicle::VEHICLE_TYPES)),
            'make_model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plate_number' => 'required|string|max:20|unique:vehicles',
            'capacity' => 'required|integer|min:1',
            'fuel_capacity' => 'required|numeric|min:0',
            'current_fuel' => 'nullable|numeric|min:0',
            'odometer_reading' => 'nullable|integer|min:0',
            'equipment_list' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Set defaults
            $validated['municipality'] = 'Maramag';
            $validated['status'] = 'available';
            $validated['is_operational'] = true;
            $validated['current_fuel'] = $validated['current_fuel'] ?? 0;
            $validated['odometer_reading'] = $validated['odometer_reading'] ?? 0;

            $vehicle = Vehicle::create($validated);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_created',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Created vehicle {$vehicle->vehicle_number}",
                'new_values' => $validated,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()
                ->route('vehicles.show', $vehicle->id)
                ->with('success', "Vehicle {$vehicle->vehicle_number} created successfully!");
        } catch (Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Display specific vehicle
     */
    public function show(int $id): View
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // Get deployment history (recent incidents where this vehicle was assigned)
            $deploymentHistory = Incident::where('assigned_vehicle', $vehicle->id)
                ->with(['reportedBy', 'assignedStaff'])
                ->orderBy('incident_datetime', 'desc')
                ->limit(10)
                ->get();

            return view('vehicles.show', compact('vehicle', 'deploymentHistory'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing vehicle
     */
    public function edit(int $id): View
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            return view('vehicles.edit', [
                'vehicle' => $vehicle,
                'vehicleTypes' => Vehicle::VEHICLE_TYPES,
                'statusOptions' => Vehicle::STATUS_OPTIONS,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Error loading vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Update vehicle
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number,' . $id,
            'vehicle_type' => 'required|in:' . implode(',', array_keys(Vehicle::VEHICLE_TYPES)),
            'make_model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $id,
            'capacity' => 'required|integer|min:1',
            'fuel_capacity' => 'required|numeric|min:0',
            'current_fuel' => 'nullable|numeric|min:0',
            'odometer_reading' => 'nullable|integer|min:0',
            'equipment_list' => 'nullable|string',
            'is_operational' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $vehicle->toArray();
            $vehicle->update($validated);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_updated',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Updated vehicle {$vehicle->vehicle_number}",
                'old_values' => $oldValues,
                'new_values' => $validated,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()
                ->route('vehicles.show', $vehicle->id)
                ->with('success', "Vehicle {$vehicle->vehicle_number} updated successfully!");
        } catch (Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Delete vehicle
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicleNumber = $vehicle->vehicle_number;

            DB::beginTransaction();

            // Log activity before deletion
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_deleted',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Deleted vehicle {$vehicleNumber}",
                'old_values' => $vehicle->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $vehicle->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle {$vehicleNumber} deleted successfully!"
                ]);
            }

            return redirect()
                ->route('vehicles.index')
                ->with('success', "Vehicle {$vehicleNumber} deleted successfully!");
        } catch (Exception $e) {
            DB::rollBack();

            // Return JSON error response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting vehicle: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Error deleting vehicle: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Update vehicle status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:available,deployed,maintenance,out_of_service'
            ]);

            $vehicle = Vehicle::findOrFail($id);
            $oldStatus = $vehicle->status;

            DB::beginTransaction();

            $vehicle->update(['status' => $request->status]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_status_updated',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Updated vehicle {$vehicle->vehicle_number} status from {$oldStatus} to {$request->status}",
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $request->status],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle status updated successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'status' => $vehicle->status,
                    'status_badge' => $this->getStatusBadgeClass($vehicle->status)
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * AJAX: Update fuel level
     */
    public function updateFuel(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'fuel_level' => 'required|numeric|min:0'
            ]);

            $vehicle = Vehicle::findOrFail($id);
            $oldFuel = $vehicle->current_fuel;

            // Validate fuel level doesn't exceed capacity
            if ($request->fuel_level > $vehicle->fuel_capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuel level cannot exceed vehicle capacity'
                ], 422);
            }

            DB::beginTransaction();

            $vehicle->update(['current_fuel' => $request->fuel_level]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_fuel_updated',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Updated vehicle {$vehicle->vehicle_number} fuel from {$oldFuel}L to {$request->fuel_level}L",
                'old_values' => ['current_fuel' => $oldFuel],
                'new_values' => ['current_fuel' => $request->fuel_level],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            $fuelPercentage = ($vehicle->current_fuel / $vehicle->fuel_capacity) * 100;

            return response()->json([
                'success' => true,
                'message' => 'Fuel level updated successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'current_fuel' => $vehicle->current_fuel,
                    'fuel_percentage' => round($fuelPercentage, 1),
                    'fuel_status_class' => $this->getFuelStatusClass($fuelPercentage)
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating fuel: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * AJAX: Schedule maintenance
     */
    public function scheduleMaintenance(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'maintenance_date' => 'required|date|after:today',
                'maintenance_notes' => 'nullable|string'
            ]);

            $vehicle = Vehicle::findOrFail($id);

            DB::beginTransaction();

            $vehicle->update([
                'status' => 'maintenance',
                'next_maintenance_due' => $request->maintenance_date
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_maintenance_scheduled',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Scheduled maintenance for vehicle {$vehicle->vehicle_number} on {$request->maintenance_date}",
                'new_values' => [
                    'status' => 'maintenance',
                    'next_maintenance_due' => $request->maintenance_date,
                    'maintenance_notes' => $request->maintenance_notes
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance scheduled successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'status' => $vehicle->status,
                    'next_maintenance_due' => $vehicle->next_maintenance_due
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error scheduling maintenance: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * AJAX: Complete maintenance
     */
    public function completeMaintenance(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'maintenance_notes' => 'nullable|string',
                'next_maintenance_date' => 'nullable|date|after:today'
            ]);

            $vehicle = Vehicle::findOrFail($id);

            DB::beginTransaction();

            $updateData = [
                'status' => 'available',
                'last_maintenance' => now(),
                'is_operational' => true
            ];

            if ($request->next_maintenance_date) {
                $updateData['next_maintenance_due'] = $request->next_maintenance_date;
            }

            $vehicle->update($updateData);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'vehicle_maintenance_completed',
                'model_type' => 'App\Models\Vehicle',
                'model_id' => $vehicle->id,
                'description' => "Completed maintenance for vehicle {$vehicle->vehicle_number}",
                'new_values' => array_merge($updateData, [
                    'maintenance_notes' => $request->maintenance_notes
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance completed successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'status' => $vehicle->status,
                    'last_maintenance' => $vehicle->last_maintenance,
                    'next_maintenance_due' => $vehicle->next_maintenance_due
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error completing maintenance: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * AJAX: Get available vehicles
     */
    public function getAvailable(): JsonResponse
    {
        try {
            $vehicles = Vehicle::where('status', 'available')
                ->where('is_operational', true)
                ->select('id', 'vehicle_number', 'vehicle_type', 'make_model')
                ->orderBy('vehicle_number')
                ->get();

            return response()->json([
                'success' => true,
                'vehicles' => $vehicles
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available vehicles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Get vehicle statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = [
                'total' => Vehicle::count(),
                'available' => Vehicle::where('status', 'available')->count(),
                'deployed' => Vehicle::where('status', 'deployed')->count(),
                'maintenance' => Vehicle::where('status', 'maintenance')->count(),
                'out_of_service' => Vehicle::where('status', 'out_of_service')->count(),
                'low_fuel_count' => Vehicle::whereRaw('(current_fuel / fuel_capacity) * 100 < 25')->count(),
                'overdue_maintenance_count' => Vehicle::where('next_maintenance_due', '<', now())->count(),
            ];

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Get vehicles needing attention
     */
    public function getNeedingAttention(): JsonResponse
    {
        try {
            $attention = [
                'low_fuel' => Vehicle::whereRaw('(current_fuel / fuel_capacity) * 100 < 25')
                    ->select('id', 'vehicle_number', 'current_fuel', 'fuel_capacity')
                    ->get()
                    ->map(function ($vehicle) {
                        $vehicle->fuel_percentage = round(($vehicle->current_fuel / $vehicle->fuel_capacity) * 100, 1);
                        return $vehicle;
                    }),
                'overdue_maintenance' => Vehicle::where('next_maintenance_due', '<', now())
                    ->select('id', 'vehicle_number', 'next_maintenance_due')
                    ->get(),
                'out_of_service' => Vehicle::where('status', 'out_of_service')
                    ->select('id', 'vehicle_number', 'status')
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'attention' => $attention
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching vehicles needing attention: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status badge CSS class
     */
    private function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'available' => 'badge-success',
            'deployed' => 'badge-primary',
            'maintenance' => 'badge-warning',
            'out_of_service' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    /**
     * Get fuel status CSS class
     */
    private function getFuelStatusClass(float $percentage): string
    {
        if ($percentage >= 50) return 'text-green-600';
        if ($percentage >= 25) return 'text-yellow-600';
        return 'text-red-600';
    }
}
