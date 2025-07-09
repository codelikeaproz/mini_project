<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasValidation;

final class Incident extends Model
{
    use HasFactory, HasValidation;

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($incident) {
            if (empty($incident->incident_number)) {
                $incident->incident_number = self::generateIncidentNumber();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'incident_number',
        'incident_type',
        'location',
        'municipality',
        'barangay',
        'latitude',
        'longitude',
        'incident_datetime',
        'description',
        'severity_level',
        'status',
        'vehicles_involved',
        'casualties_count',
        'injuries_count',
        'estimated_damage',
        'hospital_destination',
        'patient_condition',
        'medical_notes',
        'weather_condition',
        'road_condition',
        'assigned_vehicle',
        'additional_details',
        'reported_by',
        'assigned_staff',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'incident_datetime' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'vehicles_involved' => 'integer',
        'casualties_count' => 'integer',
        'injuries_count' => 'integer',
        'estimated_damage' => 'decimal:2',
        'additional_details' => 'array',
        'assigned_vehicle' => 'integer',
        'reported_by' => 'integer',
        'assigned_staff' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Available incident types based on MDRRMO requirements
     */
    public const INCIDENT_TYPES = [
        'vehicle_vs_vehicle' => 'Vehicle vs Vehicle',
        'vehicle_vs_pedestrian' => 'Vehicle vs Pedestrian',
        'vehicle_vs_animals' => 'Vehicle vs Animals',
        'vehicle_vs_property' => 'Vehicle vs Property',
        'vehicle_alone' => 'Vehicle Alone',
        'maternity' => 'Maternity',
        'stabbing_shooting' => 'Stabbing/Shooting',
        'transport_to_hospital' => 'Transport to Hospital',
    ];

    /**
     * Available severity levels
     */
    public const SEVERITY_LEVELS = [
        'minor' => 'Minor',
        'moderate' => 'Moderate',
        'severe' => 'Severe',
        'critical' => 'Critical',
    ];

    /**
     * Available status options
     */
    public const STATUS_OPTIONS = [
        'pending' => 'Pending',
        'responding' => 'Responding',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    /**
     * Available patient conditions
     */
    public const PATIENT_CONDITIONS = [
        'stable' => 'Stable',
        'critical' => 'Critical',
        'deceased' => 'Deceased',
    ];

    /**
     * Available weather conditions
     */
    public const WEATHER_CONDITIONS = [
        'clear' => 'Clear',
        'rainy' => 'Rainy',
        'foggy' => 'Foggy',
        'windy' => 'Windy',
        'stormy' => 'Stormy',
    ];

    /**
     * Available road conditions
     */
    public const ROAD_CONDITIONS = [
        'dry' => 'Dry',
        'wet' => 'Wet',
        'slippery' => 'Slippery',
        'under_construction' => 'Under Construction',
        'damaged' => 'Damaged',
    ];

    /**
     * Get the user who reported this incident
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Alias for reporter relationship (for consistency with views)
     */
    public function reportedBy(): BelongsTo
    {
        return $this->reporter();
    }

    /**
     * Get the staff member assigned to this incident
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff');
    }

    /**
     * Get the vehicle assigned to this incident
     */
    public function assignedVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle');
    }

    /**
     * Get all victims involved in this incident
     */
    public function victims(): HasMany
    {
        return $this->hasMany(Victim::class);
    }

    /**
     * Generate a unique incident number
     */
    public static function generateIncidentNumber(): string
    {
        $prefix = 'MDRRMO-' . date('Y');
        $lastIncident = self::where('incident_number', 'like', $prefix . '%')
            ->orderBy('incident_number', 'desc')
            ->first();

        if ($lastIncident) {
            $lastNumber = (int) substr($lastIncident->incident_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if incident is vehicle-related
     */
    public function isVehicleRelated(): bool
    {
        return in_array($this->incident_type, [
            'vehicle_vs_vehicle',
            'vehicle_vs_pedestrian',
            'vehicle_vs_animals',
            'vehicle_vs_property',
            'vehicle_alone',
        ]);
    }

    /**
     * Check if incident is medical emergency
     */
    public function isMedicalEmergency(): bool
    {
        return in_array($this->incident_type, [
            'maternity',
            'stabbing_shooting',
            'transport_to_hospital',
        ]);
    }

    /**
     * Get the formatted incident type
     */
    public function getFormattedIncidentTypeAttribute(): string
    {
        return self::INCIDENT_TYPES[$this->incident_type] ?? $this->incident_type;
    }

    /**
     * Get the formatted severity level
     */
    public function getFormattedSeverityLevelAttribute(): string
    {
        return self::SEVERITY_LEVELS[$this->severity_level] ?? $this->severity_level;
    }

    /**
     * Get the formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }

    /**
     * Scope for filtering by incident type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('incident_type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by municipality
     */
    public function scopeByMunicipality($query, string $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('incident_datetime', [$startDate, $endDate]);
    }

    /**
     * Get validation rules for this model
     */
    public function getValidationRules(): array
    {
        return $this->getIncidentValidationRules();
    }
}
