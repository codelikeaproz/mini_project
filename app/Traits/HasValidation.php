<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Validation Trait
 *
 * Provides reusable validation methods following DRY principle:
 * - Common validation patterns
 * - Consistent error handling
 * - Type-safe validation results
 */
trait HasValidation
{
    /**
     * Validate request data against rules
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @return array
     * @throws ValidationException
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        return $request->validate($rules, $messages);
    }

    /**
     * Validate array data against rules
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return array
     * @throws ValidationException
     */
    protected function validateData(array $data, array $rules, array $messages = []): array
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get validation rules for MDRRMO user creation
     */
    protected function getUserValidationRules(bool $isUpdate = false): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'in:admin,mdrrmo_staff'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'municipality' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ];

        if (!$isUpdate) {
            $rules['email'][] = 'unique:users,email';
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['email'][] = 'unique:users,email,' . request()->route('user');
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Get validation rules for incident creation
     */
    protected function getIncidentValidationRules(): array
    {
        return [
            'incident_type' => [
                'required',
                'in:vehicle_vs_vehicle,vehicle_vs_pedestrian,vehicle_vs_animals,vehicle_vs_property,vehicle_alone,maternity,stabbing_shooting,transport_to_hospital'
            ],
            'location' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:100'],
            'barangay' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'incident_datetime' => ['required', 'date'],
            'description' => ['required', 'string'],
            'severity_level' => ['required', 'in:minor,moderate,severe,critical'],
            'vehicles_involved' => ['integer', 'min:0'],
            'casualties_count' => ['integer', 'min:0'],
            'injuries_count' => ['integer', 'min:0'],
            'estimated_damage' => ['nullable', 'numeric', 'min:0'],
            'hospital_destination' => ['nullable', 'string', 'max:255'],
            'patient_condition' => ['nullable', 'in:stable,critical,deceased'],
            'medical_notes' => ['nullable', 'string'],
            'weather_condition' => ['nullable', 'in:clear,rainy,foggy,windy,stormy'],
            'road_condition' => ['nullable', 'in:dry,wet,slippery,under_construction,damaged'],
            'assigned_vehicle' => ['nullable', 'exists:vehicles,id'],
            'additional_details' => ['nullable', 'array'],
        ];
    }

    /**
     * Get validation rules for vehicle creation
     */
    protected function getVehicleValidationRules(bool $isUpdate = false): array
    {
        $rules = [
            'vehicle_number' => ['required', 'string', 'max:50'],
            'vehicle_type' => ['required', 'in:ambulance,fire_truck,rescue_vehicle,patrol_car,motorcycle,emergency_van'],
            'make_model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'plate_number' => ['required', 'string', 'max:20'],
            'status' => ['required', 'in:available,deployed,maintenance,out_of_service'],
            'municipality' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
            'fuel_capacity' => ['required', 'numeric', 'min:0'],
            'current_fuel' => ['nullable', 'numeric', 'min:0'],
            'odometer_reading' => ['nullable', 'integer', 'min:0'],
            'last_maintenance' => ['nullable', 'date'],
            'next_maintenance_due' => ['nullable', 'date'],
            'equipment_list' => ['nullable', 'string'],
            'is_operational' => ['boolean'],
        ];

        if (!$isUpdate) {
            $rules['vehicle_number'][] = 'unique:vehicles,vehicle_number';
            $rules['plate_number'][] = 'unique:vehicles,plate_number';
        } else {
            $rules['vehicle_number'][] = 'unique:vehicles,vehicle_number,' . request()->route('vehicle');
            $rules['plate_number'][] = 'unique:vehicles,plate_number,' . request()->route('vehicle');
        }

        return $rules;
    }

    /**
     * Get validation rules for victim information
     */
    protected function getVictimValidationRules(): array
    {
        return [
            'incident_id' => ['required', 'exists:incidents,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', 'in:male,female,other'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'involvement_type' => [
                'required',
                'in:driver,passenger,pedestrian,witness,patient,expectant_mother,victim,property_owner,other'
            ],
            'injury_status' => [
                'required',
                'in:none,minor_injury,serious_injury,critical_condition,in_labor,gunshot_wound,stab_wound,fatal'
            ],
            'hospital_referred' => ['nullable', 'string', 'max:255'],
            'hospital_arrival_time' => ['nullable', 'date'],
            'medical_notes' => ['nullable', 'string'],
            'transport_method' => ['nullable', 'in:ambulance,private_vehicle,motorcycle,helicopter,walk_in'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'vehicle_plate_number' => ['nullable', 'string', 'max:20'],
            'wearing_helmet' => ['nullable', 'boolean'],
            'wearing_seatbelt' => ['nullable', 'boolean'],
            'license_status' => ['nullable', 'in:valid,expired,no_license,unknown'],
            'emergency_contacts' => ['nullable', 'array'],
        ];
    }

    /**
     * Get common error messages
     */
    protected function getValidationMessages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a text value.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'integer' => 'The :attribute must be a number.',
            'numeric' => 'The :attribute must be a numeric value.',
            'date' => 'The :attribute must be a valid date.',
            'boolean' => 'The :attribute must be true or false.',
            'in' => 'The selected :attribute is invalid.',
            'exists' => 'The selected :attribute does not exist.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'between' => 'The :attribute must be between :min and :max.',
        ];
    }

    /**
     * Validate file upload
     */
    protected function validateFileUpload(Request $request, string $field, array $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], int $maxSize = 2048): void
    {
        $rules = [
            $field => [
                'required',
                'file',
                'max:' . $maxSize,
                'mimes:' . implode(',', $allowedTypes)
            ]
        ];

        $messages = [
            $field . '.required' => 'Please select a file to upload.',
            $field . '.file' => 'The uploaded item must be a file.',
            $field . '.max' => 'The file size cannot exceed ' . $maxSize . 'KB.',
            $field . '.mimes' => 'The file must be of type: ' . implode(', ', $allowedTypes) . '.',
        ];

        $this->validateRequest($request, $rules, $messages);
    }

    /**
     * Validate date range
     */
    protected function validateDateRange(string $startDate, string $endDate): void
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        if ($start->gt($end)) {
            throw ValidationException::withMessages([
                'date_range' => 'Start date must be before or equal to end date.'
            ]);
        }

        if ($start->diffInDays($end) > 365) {
            throw ValidationException::withMessages([
                'date_range' => 'Date range cannot exceed 365 days.'
            ]);
        }
    }

    /**
     * Validate coordinates
     */
    protected function validateCoordinates(?float $latitude, ?float $longitude): void
    {
        if (($latitude !== null && $longitude === null) || ($latitude === null && $longitude !== null)) {
            throw ValidationException::withMessages([
                'coordinates' => 'Both latitude and longitude must be provided together.'
            ]);
        }

        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            throw ValidationException::withMessages([
                'latitude' => 'Latitude must be between -90 and 90 degrees.'
            ]);
        }

        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            throw ValidationException::withMessages([
                'longitude' => 'Longitude must be between -180 and 180 degrees.'
            ]);
        }
    }
}
