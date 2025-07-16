<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Victim;
use App\Models\Incident;

final class VictimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing incidents
        $incidents = Incident::all();

        if ($incidents->count() === 0) {
            $this->command->warn('No incidents found. Please seed incidents first.');
            return;
        }

        // Sample victim data for different incident types
        $victimData = [
            // Vehicle accidents
            [
                'first_name' => 'Juan',
                'last_name' => 'Santos',
                'age' => 35,
                'gender' => 'male',
                'contact_number' => '09123456789',
                'address' => 'Purok 1, Poblacion, Maramag, Bukidnon',
                'involvement_type' => 'driver',
                'injury_status' => 'minor_injury',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Minor cuts and bruises from vehicle collision',
                'transport_method' => 'ambulance',
                'vehicle_type' => 'motorcycle',
                'vehicle_plate_number' => 'ABC-123',
                'wearing_helmet' => true,
                'license_status' => 'valid',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Cruz',
                'age' => 28,
                'gender' => 'female',
                'contact_number' => '09234567890',
                'address' => 'Purok 3, Santo Niño, Maramag, Bukidnon',
                'involvement_type' => 'passenger',
                'injury_status' => 'serious_injury',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Broken arm and possible internal injuries',
                'transport_method' => 'ambulance',
                'wearing_seatbelt' => false,
            ],
            [
                'first_name' => 'Pedro',
                'last_name' => 'Reyes',
                'age' => 42,
                'gender' => 'male',
                'contact_number' => '09345678901',
                'address' => 'Barangay Kalawakan, Maramag, Bukidnon',
                'involvement_type' => 'pedestrian',
                'injury_status' => 'critical_condition',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Head trauma, requires immediate surgery',
                'transport_method' => 'ambulance',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'age' => 22,
                'gender' => 'female',
                'contact_number' => '09456789012',
                'address' => 'Barangay Aglayan, Maramag, Bukidnon',
                'involvement_type' => 'driver',
                'injury_status' => 'minor_injury',
                'medical_notes' => 'Minor scratches, refused hospital transport',
                'vehicle_type' => 'motorcycle',
                'vehicle_plate_number' => 'DEF-456',
                'wearing_helmet' => false,
                'license_status' => 'valid',
            ],

            // Medical emergencies
            [
                'first_name' => 'Elena',
                'last_name' => 'Mendoza',
                'age' => 26,
                'gender' => 'female',
                'contact_number' => '09567890123',
                'address' => 'Purok 2, Poblacion, Maramag, Bukidnon',
                'involvement_type' => 'patient',
                'injury_status' => 'in_labor',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Emergency delivery, mother and baby stable',
                'transport_method' => 'ambulance',
            ],
            [
                'first_name' => 'Roberto',
                'last_name' => 'Fernandez',
                'age' => 58,
                'gender' => 'male',
                'contact_number' => '09678901234',
                'address' => 'Barangay Guinoyuran, Maramag, Bukidnon',
                'involvement_type' => 'patient',
                'injury_status' => 'critical_condition',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Chest pain, possible heart attack',
                'transport_method' => 'ambulance',
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Ramirez',
                'age' => 31,
                'gender' => 'male',
                'contact_number' => '09789012345',
                'address' => 'Purok 4, Poblacion, Maramag, Bukidnon',
                'involvement_type' => 'victim',
                'injury_status' => 'stab_wound',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Multiple stab wounds to torso, emergency surgery performed',
                'transport_method' => 'ambulance',
            ],

            // Additional victims for other incidents
            [
                'first_name' => 'Lisa',
                'last_name' => 'Torres',
                'age' => 19,
                'gender' => 'female',
                'contact_number' => '09890123456',
                'address' => 'Barangay Bantuanon, Maramag, Bukidnon',
                'involvement_type' => 'expectant_mother',
                'injury_status' => 'in_labor',
                'hospital_referred' => 'Bukidnon Provincial Hospital',
                'medical_notes' => 'Active labor, transported during storm',
                'transport_method' => 'ambulance',
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Valdez',
                'age' => 45,
                'gender' => 'male',
                'contact_number' => '09901234567',
                'address' => 'Barangay San Roque, Maramag, Bukidnon',
                'involvement_type' => 'driver',
                'injury_status' => 'none',
                'medical_notes' => 'No injuries reported in minor collision',
                'vehicle_type' => 'motorcycle',
                'vehicle_plate_number' => 'GHI-789',
                'wearing_helmet' => true,
                'license_status' => 'valid',
            ],
        ];

        echo "👥 Creating victim records...\n";

        foreach ($victimData as $index => $data) {
            // Assign victims to incidents sequentially
            $incident = $incidents->get($index % $incidents->count());
            $data['incident_id'] = $incident->id;

            $victim = Victim::create($data);

            echo "✅ Created victim: {$victim->first_name} {$victim->last_name} for incident #{$incident->incident_number}\n";
        }

        echo "\n🎯 Successfully seeded " . count($victimData) . " victim records!\n";
        echo "📊 Distribution:\n";
        echo "   • Vehicle-related injuries: " . collect($victimData)->filter(fn($v) => in_array($v['involvement_type'], ['driver', 'passenger', 'pedestrian']))->count() . "\n";
        echo "   • Medical patients: " . collect($victimData)->filter(fn($v) => in_array($v['involvement_type'], ['patient', 'victim', 'expectant_mother']))->count() . "\n";
        echo "   • Requiring hospital transport: " . collect($victimData)->filter(fn($v) => isset($v['hospital_referred']))->count() . "\n";
    }
}
