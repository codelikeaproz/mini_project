<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Exception;

final class VehicleController extends Controller
{
    public function __construct(
        private readonly VehicleService $vehicleService
    ) {
        $this->middleware(['auth', 'verified', 'role:admin,mdrrmo_staff']);
    }

    /**
     * Display vehicle listing with filtering
     */
    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'type' => $request->get('type'),
                'status' => $request->get('status'),
                'operational' => $request->has('operational') ? $request->boolean('operational') : null,
                'municipality' => $request->get('municipality')
            ];

            $vehicles = $this->vehicleService->getVehicles($filters, 15);
            $statistics = $this->vehicleService->getStatistics();
            $attention = $this->vehicleService->getVehiclesNeedingAttention();

            return view('vehicles.index', compact('vehicles', 'statistics', 'attention', 'filters'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading vehicles: ' . $e->getMessage());
        }
    }

    /**
     * Show form for creating new vehicle
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Store new vehicle
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $vehicle = $this->vehicleService->createVehicle($request->all());

            return redirect()
                ->route('vehicles.show', $vehicle->id)
                ->with('success', "Vehicle {$vehicle->vehicle_number} created successfully!");
        } catch (Exception $e) {
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
            $vehicle = $this->vehicleService->getVehicle($id);
            $deploymentHistory = $this->vehicleService->getDeploymentHistory($id);

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
            $vehicle = $this->vehicleService->getVehicle($id);
            return view('vehicles.edit', compact('vehicle'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Update vehicle
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $vehicle = $this->vehicleService->updateVehicle($id, $request->all());

            return redirect()
                ->route('vehicles.show', $vehicle->id)
                ->with('success', "Vehicle {$vehicle->vehicle_number} updated successfully!");
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Delete vehicle
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $vehicle = $this->vehicleService->getVehicle($id);
            $vehicleNumber = $vehicle->vehicle_number;

            $this->vehicleService->deleteVehicle($id);

            return redirect()
                ->route('vehicles.index')
                ->with('success', "Vehicle {$vehicleNumber} deleted successfully!");
        } catch (Exception $e) {
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

            $vehicle = $this->vehicleService->updateStatus($id, $request->status);

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

            $vehicle = $this->vehicleService->updateFuel($id, $request->fuel_level);

            return response()->json([
                'success' => true,
                'message' => 'Fuel level updated successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'current_fuel' => $vehicle->current_fuel,
                    'fuel_percentage' => $vehicle->fuel_percentage,
                    'fuel_status_class' => $this->getFuelStatusClass($vehicle->fuel_percentage)
                ]
            ]);
        } catch (Exception $e) {
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
                'maintenance_date' => 'required|date|after:today'
            ]);

            $maintenanceDate = Carbon::parse($request->maintenance_date);
            $vehicle = $this->vehicleService->scheduleMaintenance($id, $maintenanceDate);

            return response()->json([
                'success' => true,
                'message' => 'Maintenance scheduled successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'status' => $vehicle->status,
                    'next_maintenance_due' => $vehicle->next_maintenance_due,
                    'status_badge' => $this->getStatusBadgeClass($vehicle->status)
                ]
            ]);
        } catch (Exception $e) {
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
                'odometer_reading' => 'nullable|integer|min:0',
                'refuel' => 'nullable|boolean',
                'next_maintenance_due' => 'nullable|date|after:today'
            ]);

            $maintenanceData = [];

            if ($request->has('odometer_reading')) {
                $maintenanceData['odometer_reading'] = $request->odometer_reading;
            }

            if ($request->boolean('refuel')) {
                $maintenanceData['refuel'] = true;
            }

            if ($request->has('next_maintenance_due')) {
                $maintenanceData['next_maintenance_due'] = Carbon::parse($request->next_maintenance_due);
            }

            $vehicle = $this->vehicleService->completeMaintenance($id, $maintenanceData);

            return response()->json([
                'success' => true,
                'message' => 'Maintenance completed successfully',
                'vehicle' => [
                    'id' => $vehicle->id,
                    'status' => $vehicle->status,
                    'current_fuel' => $vehicle->current_fuel,
                    'fuel_percentage' => $vehicle->fuel_percentage,
                    'last_maintenance' => $vehicle->last_maintenance,
                    'next_maintenance_due' => $vehicle->next_maintenance_due,
                    'odometer_reading' => $vehicle->odometer_reading,
                    'status_badge' => $this->getStatusBadgeClass($vehicle->status),
                    'fuel_status_class' => $this->getFuelStatusClass($vehicle->fuel_percentage)
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing maintenance: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * API: Get available vehicles for incident assignment
     */
    public function getAvailable(): JsonResponse
    {
        try {
            $vehicles = $this->vehicleService->getAvailableVehicles();

            return response()->json([
                'success' => true,
                'vehicles' => $vehicles->map(function ($vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'vehicle_number' => $vehicle->vehicle_number,
                        'vehicle_type' => $vehicle->vehicle_type,
                        'make_model' => $vehicle->make_model,
                        'capacity' => $vehicle->capacity,
                        'fuel_percentage' => $vehicle->fuel_percentage
                    ];
                })
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading available vehicles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get vehicle statistics for dashboard
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->vehicleService->getStatistics();

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get vehicles needing attention
     */
    public function getNeedingAttention(): JsonResponse
    {
        try {
            $attention = $this->vehicleService->getVehiclesNeedingAttention();

            return response()->json([
                'success' => true,
                'attention' => [
                    'maintenance' => $attention['maintenance']->map(function ($vehicle) {
                        return [
                            'id' => $vehicle->id,
                            'vehicle_number' => $vehicle->vehicle_number,
                            'vehicle_type' => $vehicle->vehicle_type,
                            'next_maintenance_due' => $vehicle->next_maintenance_due,
                            'days_overdue' => $vehicle->next_maintenance_due ?
                                Carbon::now()->diffInDays($vehicle->next_maintenance_due, false) : null
                        ];
                    }),
                    'low_fuel' => $attention['low_fuel']->map(function ($vehicle) {
                        return [
                            'id' => $vehicle->id,
                            'vehicle_number' => $vehicle->vehicle_number,
                            'vehicle_type' => $vehicle->vehicle_type,
                            'fuel_percentage' => $vehicle->fuel_percentage
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading vehicles needing attention: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Get status badge CSS class
     */
    private function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'available' => 'badge bg-success',
            'deployed' => 'badge bg-warning',
            'maintenance' => 'badge bg-info',
            'out_of_service' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Helper: Get fuel status CSS class
     */
    private function getFuelStatusClass(float $percentage): string
    {
        return match (true) {
            $percentage >= 75 => 'text-success',
            $percentage >= 50 => 'text-warning',
            $percentage >= 25 => 'text-warning',
            default => 'text-danger'
        };
    }
}
