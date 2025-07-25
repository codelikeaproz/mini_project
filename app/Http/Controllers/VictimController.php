<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Victim;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * VictimController
 *
 * Handles victim management operations for MDRRMO Emergency Response System
 * Manages victim data linked to specific incidents with medical status tracking
 */
class VictimController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:admin,mdrrmo_staff');
    }

    /**
     * Display a listing of all victims
     */
    public function index(Request $request): View
    {
        $query = Victim::with(['incident']);

        // Add filtering capabilities
        if ($request->filled('incident_id')) {
            $query->where('incident_id', $request->incident_id);
        }

        if ($request->filled('injury_status')) {
            $query->where('injury_status', $request->injury_status);
        }

        if ($request->filled('involvement_type')) {
            $query->where('involvement_type', $request->involvement_type);
        }

        $victims = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('victims.index', compact('victims'));
    }

    /**
     * Show the form for creating a new victim
     */
    public function create(Request $request): View
    {
        $incident_id = $request->get('incident_id');
        $incident = null;

        if ($incident_id) {
            $incident = Incident::findOrFail($incident_id);
        }

        $incidents = Incident::select('id', 'incident_type', 'location', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit for performance
            ->get();

        return view('victims.create', compact('incidents', 'incident'));
    }

    /**
     * Store a newly created victim in storage
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validateVictimData($request);

        try {
            DB::beginTransaction();

            $victim = Victim::create($validated);

            // Log activity
            Log::info('Victim created', [
                'victim_id' => $victim->id,
                'incident_id' => $victim->incident_id,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Person has been added successfully.',
                    'victim' => $victim->load('incident')
                ]);
            }

            return redirect()
                ->route('incidents.show', $victim->incident_id)
                ->with('success', 'Victim information has been recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating victim', ['error' => $e->getMessage()]);

            // Return JSON error response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record person information. Please try again.'
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to record victim information. Please try again.');
        }
    }

    /**
     * Display the specified victim
     */
    public function show(Victim $victim): View
    {
        $victim->load(['incident']);

        return view('victims.show', compact('victim'));
    }

    /**
     * Show the form for editing the specified victim
     */
    public function edit(Victim $victim): View
    {
        $victim->load(['incident']);

        $incidents = Incident::select('id', 'incident_type', 'location', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit for performance
            ->get();

        return view('victims.edit', compact('victim', 'incidents'));
    }

    /**
     * Update the specified victim in storage
     */
    public function update(Request $request, Victim $victim): RedirectResponse|JsonResponse
    {
        $validated = $this->validateVictimData($request);

        try {
            DB::beginTransaction();

            $victim->update($validated);

            // Log activity
            Log::info('Victim updated', [
                'victim_id' => $victim->id,
                'incident_id' => $victim->incident_id,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Person information has been updated successfully.',
                    'victim' => $victim->load('incident')
                ]);
            }

            return redirect()
                ->route('victims.show', $victim)
                ->with('success', 'Victim information has been updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating victim', ['error' => $e->getMessage()]);

            // Return JSON error response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update person information. Please try again.'
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update victim information. Please try again.');
        }
    }

    /**
     * Remove the specified victim from storage
     */
    public function destroy(Request $request, Victim $victim): RedirectResponse|JsonResponse
    {
        try {
            DB::beginTransaction();

            $incident_id = $victim->incident_id;

            // Log activity before deletion
            Log::info('Victim deleted', [
                'victim_id' => $victim->id,
                'incident_id' => $incident_id,
                'deleted_by' => auth()->id()
            ]);

            $victim->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Person has been removed successfully.'
                ]);
            }

            return redirect()
                ->route('incidents.show', $incident_id)
                ->with('success', 'Victim record has been deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting victim', ['error' => $e->getMessage()]);

            // Return JSON error response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove person. Please try again.'
                ], 422);
            }

            return back()
                ->with('error', 'Failed to delete victim record. Please try again.');
        }
    }

    /**
     * Get victims for a specific incident (AJAX)
     */
    public function getByIncident(Request $request, Incident $incident): JsonResponse
    {
        $victims = $incident->victims()
            ->select('id', 'first_name', 'last_name', 'age', 'gender', 'injury_status', 'involvement_type', 'hospital_referred')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'victims' => $victims,
            'count' => $victims->count()
        ]);
    }

    /**
     * Get a specific victim data for editing (AJAX)
     */
    public function getVictim(Request $request, Victim $victim): JsonResponse
    {
        return response()->json([
            'success' => true,
            'victim' => $victim
        ]);
    }

    /**
     * Get victim statistics for dashboard
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total' => Victim::count(),
            'by_injury_status' => Victim::select('injury_status', DB::raw('count(*) as count'))
                ->groupBy('injury_status')
                ->pluck('count', 'injury_status'),
            'by_involvement_type' => Victim::select('involvement_type', DB::raw('count(*) as count'))
                ->groupBy('involvement_type')
                ->pluck('count', 'involvement_type'),
            'recent_critical' => Victim::where('injury_status', 'critical_condition')
                ->where('created_at', '>=', now()->subDays(7))
                ->count()
        ];

        return response()->json($stats);
    }

    /**
     * Validate victim data - DRY principle
     */
    private function validateVictimData(Request $request): array
    {
        return $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'involvement_type' => 'required|in:driver,passenger,pedestrian,witness,patient,expectant_mother,victim,property_owner,other',
            'injury_status' => 'required|in:none,minor_injury,serious_injury,critical_condition,in_labor,gunshot_wound,stab_wound,fatal',
            'hospital_referred' => 'nullable|string|max:255',
            'hospital_arrival_time' => 'nullable|date',
            'medical_notes' => 'nullable|string|max:1000',
            'transport_method' => 'nullable|in:ambulance,private_vehicle,motorcycle,helicopter,walk_in',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_plate_number' => 'nullable|string|max:20',
            'wearing_helmet' => 'nullable|boolean',
            'wearing_seatbelt' => 'nullable|boolean',
            'license_status' => 'nullable|in:valid,expired,no_license,unknown',
            'emergency_contacts' => 'nullable|array'
        ]);
    }
}
