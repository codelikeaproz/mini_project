<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasValidation;

final class Vehicle extends Model
{
    use HasFactory, HasValidation;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vehicle_number',
        'vehicle_type',
        'make_model',
        'year',
        'plate_number',
        'status',
        'municipality',
        'capacity',
        'fuel_capacity',
        'current_fuel',
        'odometer_reading',
        'last_maintenance',
        'next_maintenance_due',
        'equipment_list',
        'is_operational',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'year' => 'integer',
        'capacity' => 'integer',
        'fuel_capacity' => 'decimal:2',
        'current_fuel' => 'decimal:2',
        'odometer_reading' => 'integer',
        'last_maintenance' => 'date',
        'next_maintenance_due' => 'date',
        'is_operational' => 'boolean',
    ];

    /**
     * Available vehicle types
     */
    public const VEHICLE_TYPES = [
        'ambulance' => 'Ambulance',
        'fire_truck' => 'Fire Truck',
        'rescue_vehicle' => 'Rescue Vehicle',
        'patrol_car' => 'Patrol Car',
        'motorcycle' => 'Motorcycle',
        'emergency_van' => 'Emergency Van',
    ];

    /**
     * Available vehicle status options
     */
    public const STATUS_OPTIONS = [
        'available' => 'Available',
        'deployed' => 'Deployed',
        'maintenance' => 'Under Maintenance',
        'out_of_service' => 'Out of Service',
    ];

    /**
     * Get all incidents assigned to this vehicle
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_vehicle');
    }

    /**
     * Get the formatted vehicle type
     */
    public function getFormattedVehicleTypeAttribute(): string
    {
        return self::VEHICLE_TYPES[$this->vehicle_type] ?? $this->vehicle_type;
    }

    /**
     * Get the formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    /**
     * Get the fuel percentage
     */
    public function getFuelPercentageAttribute(): float
    {
        if ($this->fuel_capacity > 0) {
            return round(($this->current_fuel / $this->fuel_capacity) * 100, 1);
        }
        return 0.0;
    }

    /**
     * Check if vehicle needs maintenance soon
     */
    public function needsMaintenanceSoon(): bool
    {
        if (!$this->next_maintenance_due) {
            return false;
        }

        return now()->diffInDays($this->next_maintenance_due) <= 7;
    }

    /**
     * Check if vehicle is overdue for maintenance
     */
    public function isMaintenanceOverdue(): bool
    {
        if (!$this->next_maintenance_due) {
            return false;
        }

        return now()->gt($this->next_maintenance_due);
    }

    /**
     * Check if vehicle is available for deployment
     */
    public function isAvailableForDeployment(): bool
    {
        return $this->status === 'available' &&
               $this->is_operational &&
               !$this->isMaintenanceOverdue();
    }

    /**
     * Get maintenance status
     */
    public function getMaintenanceStatusAttribute(): string
    {
        if ($this->isMaintenanceOverdue()) {
            return 'overdue';
        }

        if ($this->needsMaintenanceSoon()) {
            return 'due_soon';
        }

        return 'current';
    }

    /**
     * Scope for filtering by vehicle type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('vehicle_type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for available vehicles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where('is_operational', true);
    }

    /**
     * Scope for operational vehicles
     */
    public function scopeOperational($query)
    {
        return $query->where('is_operational', true);
    }

    /**
     * Scope for vehicles needing maintenance
     */
    public function scopeNeedingMaintenance($query)
    {
        return $query->where(function ($q) {
            $q->where('next_maintenance_due', '<=', now()->addDays(7))
              ->orWhere('next_maintenance_due', '<', now());
        });
    }

    /**
     * Generate a unique vehicle number
     */
    public static function generateVehicleNumber(): string
    {
        $prefix = 'MDR-';
        $lastVehicle = self::where('vehicle_number', 'like', $prefix . '%')
            ->orderBy('vehicle_number', 'desc')
            ->first();

        if ($lastVehicle) {
            $lastNumber = (int) substr($lastVehicle->vehicle_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get validation rules for this model
     */
    public function getValidationRules(): array
    {
        return $this->getVehicleValidationRules();
    }
}
