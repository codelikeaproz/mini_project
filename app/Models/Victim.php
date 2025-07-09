<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasValidation;

final class Victim extends Model
{
    use HasFactory, HasValidation;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'incident_id',
        'first_name',
        'last_name',
        'age',
        'gender',
        'contact_number',
        'address',
        'involvement_type',
        'injury_status',
        'hospital_referred',
        'hospital_arrival_time',
        'medical_notes',
        'transport_method',
        'vehicle_type',
        'vehicle_plate_number',
        'wearing_helmet',
        'wearing_seatbelt',
        'license_status',
        'emergency_contacts',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'age' => 'integer',
        'hospital_arrival_time' => 'datetime',
        'wearing_helmet' => 'boolean',
        'wearing_seatbelt' => 'boolean',
        'emergency_contacts' => 'array',
        'incident_id' => 'integer',
    ];

    /**
     * Available involvement types
     */
    public const INVOLVEMENT_TYPES = [
        'driver' => 'Driver',
        'passenger' => 'Passenger',
        'pedestrian' => 'Pedestrian',
        'witness' => 'Witness',
        'patient' => 'Patient',
        'expectant_mother' => 'Expectant Mother',
        'victim' => 'Victim',
        'property_owner' => 'Property Owner',
        'other' => 'Other',
    ];

    /**
     * Available injury status options
     */
    public const INJURY_STATUS_OPTIONS = [
        'none' => 'No Injury',
        'minor_injury' => 'Minor Injury',
        'serious_injury' => 'Serious Injury',
        'critical_condition' => 'Critical Condition',
        'in_labor' => 'In Labor',
        'gunshot_wound' => 'Gunshot Wound',
        'stab_wound' => 'Stab Wound',
        'fatal' => 'Fatal',
    ];

    /**
     * Available transport methods
     */
    public const TRANSPORT_METHODS = [
        'ambulance' => 'Ambulance',
        'private_vehicle' => 'Private Vehicle',
        'motorcycle' => 'Motorcycle',
        'helicopter' => 'Helicopter',
        'walk_in' => 'Walk-in',
    ];

    /**
     * Available license status options
     */
    public const LICENSE_STATUS_OPTIONS = [
        'valid' => 'Valid License',
        'expired' => 'Expired License',
        'no_license' => 'No License',
        'unknown' => 'Unknown',
    ];

    /**
     * Available gender options
     */
    public const GENDER_OPTIONS = [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
    ];

    /**
     * Get the incident this victim is involved in
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the full name of the victim
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the formatted involvement type
     */
    public function getFormattedInvolvementTypeAttribute(): string
    {
        return self::INVOLVEMENT_TYPES[$this->involvement_type] ?? $this->involvement_type;
    }

    /**
     * Get the formatted injury status
     */
    public function getFormattedInjuryStatusAttribute(): string
    {
        return self::INJURY_STATUS_OPTIONS[$this->injury_status] ?? $this->injury_status;
    }

    /**
     * Get the formatted transport method
     */
    public function getFormattedTransportMethodAttribute(): ?string
    {
        if (!$this->transport_method) {
            return null;
        }
        return self::TRANSPORT_METHODS[$this->transport_method] ?? $this->transport_method;
    }

    /**
     * Get the formatted license status
     */
    public function getFormattedLicenseStatusAttribute(): ?string
    {
        if (!$this->license_status) {
            return null;
        }
        return self::LICENSE_STATUS_OPTIONS[$this->license_status] ?? $this->license_status;
    }

    /**
     * Get the formatted gender
     */
    public function getFormattedGenderAttribute(): ?string
    {
        if (!$this->gender) {
            return null;
        }
        return self::GENDER_OPTIONS[$this->gender] ?? $this->gender;
    }

    /**
     * Check if victim has serious or critical injuries
     */
    public function hasSeriousInjuries(): bool
    {
        return in_array($this->injury_status, [
            'serious_injury',
            'critical_condition',
            'gunshot_wound',
            'stab_wound',
            'fatal',
        ]);
    }

    /**
     * Check if victim is deceased
     */
    public function isDeceased(): bool
    {
        return $this->injury_status === 'fatal';
    }

    /**
     * Check if victim requires medical attention
     */
    public function requiresMedicalAttention(): bool
    {
        return !in_array($this->injury_status, ['none']);
    }

    /**
     * Check if victim was transported to hospital
     */
    public function wasTransportedToHospital(): bool
    {
        return !empty($this->hospital_referred) || !empty($this->hospital_arrival_time);
    }

    /**
     * Get injury severity level (for prioritization)
     */
    public function getInjurySeverityLevelAttribute(): int
    {
        return match ($this->injury_status) {
            'fatal' => 5,
            'critical_condition', 'gunshot_wound', 'stab_wound' => 4,
            'serious_injury' => 3,
            'minor_injury', 'in_labor' => 2,
            'none' => 1,
            default => 0,
        };
    }

    /**
     * Scope for filtering by involvement type
     */
    public function scopeByInvolvementType($query, string $type)
    {
        return $query->where('involvement_type', $type);
    }

    /**
     * Scope for filtering by injury status
     */
    public function scopeByInjuryStatus($query, string $status)
    {
        return $query->where('injury_status', $status);
    }

    /**
     * Scope for victims with serious injuries
     */
    public function scopeWithSeriousInjuries($query)
    {
        return $query->whereIn('injury_status', [
            'serious_injury',
            'critical_condition',
            'gunshot_wound',
            'stab_wound',
            'fatal',
        ]);
    }

    /**
     * Scope for victims requiring medical attention
     */
    public function scopeRequiringMedicalAttention($query)
    {
        return $query->where('injury_status', '!=', 'none');
    }

    /**
     * Scope for deceased victims
     */
    public function scopeDeceased($query)
    {
        return $query->where('injury_status', 'fatal');
    }

    /**
     * Get validation rules for this model
     */
    public function getValidationRules(): array
    {
        return $this->getVictimValidationRules();
    }
}
