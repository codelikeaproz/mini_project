<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

final class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'vehicle_number' => 'MDR-001',
                'vehicle_type' => 'ambulance',
                'make_model' => 'Toyota Hiace Ambulance',
                'year' => 2022,
                'plate_number' => 'ABC-123',
                'status' => 'available',
                'municipality' => 'Maramag',
                'capacity' => 4,
                'fuel_capacity' => 60.00,
                'current_fuel' => 55.00,
                'odometer_reading' => 15000,
                'last_maintenance' => '2024-12-01',
                'next_maintenance_due' => '2025-03-01',
                'equipment_list' => 'Stretcher, Oxygen Tank, Defibrillator, First Aid Kit',
                'is_operational' => true,
            ],
            [
                'vehicle_number' => 'MDR-002',
                'vehicle_type' => 'fire_truck',
                'make_model' => 'Isuzu Fire Truck',
                'year' => 2021,
                'plate_number' => 'DEF-456',
                'status' => 'available',
                'municipality' => 'Maramag',
                'capacity' => 6,
                'fuel_capacity' => 100.00,
                'current_fuel' => 80.00,
                'odometer_reading' => 25000,
                'last_maintenance' => '2024-11-15',
                'next_maintenance_due' => '2025-02-15',
                'equipment_list' => 'Water Hose, Ladder, Fire Extinguisher, Rescue Tools',
                'is_operational' => true,
            ],
            [
                'vehicle_number' => 'MDR-003',
                'vehicle_type' => 'rescue_vehicle',
                'make_model' => 'Ford Ranger Rescue',
                'year' => 2023,
                'plate_number' => 'GHI-789',
                'status' => 'available',
                'municipality' => 'Maramag',
                'capacity' => 5,
                'fuel_capacity' => 80.00,
                'current_fuel' => 70.00,
                'odometer_reading' => 8000,
                'last_maintenance' => '2024-12-15',
                'next_maintenance_due' => '2025-04-15',
                'equipment_list' => 'Rope, Cutting Tools, Communication Equipment, Emergency Lights',
                'is_operational' => true,
            ],
            [
                'vehicle_number' => 'MDR-004',
                'vehicle_type' => 'patrol_car',
                'make_model' => 'Toyota Vios Patrol',
                'year' => 2020,
                'plate_number' => 'JKL-012',
                'status' => 'deployed',
                'municipality' => 'Maramag',
                'capacity' => 5,
                'fuel_capacity' => 42.00,
                'current_fuel' => 35.00,
                'odometer_reading' => 45000,
                'last_maintenance' => '2024-10-01',
                'next_maintenance_due' => '2025-01-01',
                'equipment_list' => 'Radio, Emergency Lights, First Aid Kit',
                'is_operational' => true,
            ],
            [
                'vehicle_number' => 'MDR-005',
                'vehicle_type' => 'motorcycle',
                'make_model' => 'Honda XRM 125',
                'year' => 2022,
                'plate_number' => 'MNO-345',
                'status' => 'available',
                'municipality' => 'Maramag',
                'capacity' => 2,
                'fuel_capacity' => 6.50,
                'current_fuel' => 5.00,
                'odometer_reading' => 12000,
                'last_maintenance' => '2024-11-01',
                'next_maintenance_due' => '2025-02-01',
                'equipment_list' => 'Communication Radio, Emergency Kit',
                'is_operational' => true,
            ],
            [
                'vehicle_number' => 'MDR-006',
                'vehicle_type' => 'emergency_van',
                'make_model' => 'Mitsubishi L300 Van',
                'year' => 2019,
                'plate_number' => 'PQR-678',
                'status' => 'maintenance',
                'municipality' => 'Maramag',
                'capacity' => 8,
                'fuel_capacity' => 55.00,
                'current_fuel' => 10.00,
                'odometer_reading' => 67000,
                'last_maintenance' => '2024-12-20',
                'next_maintenance_due' => '2024-12-25',
                'equipment_list' => 'Emergency Supplies, Communication Equipment, Medical Kit',
                'is_operational' => false,
            ]
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

        $this->command->info('✅ Successfully seeded ' . count($vehicles) . ' vehicles for MDRRMO system');
    }
}
