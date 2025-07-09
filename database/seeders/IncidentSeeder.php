<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;

final class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and vehicles
        $admin = User::where('role', 'admin')->first();
        $staffMembers = User::where('role', 'mdrrmo_staff')->get();
        $vehicles = Vehicle::all();

        // Sample barangays in Maramag
        $barangays = [
            'Poblacion', 'Aglayan', 'Aniag', 'Bacusanon', 'Bantuanon',
            'Borodan', 'Bugcaon', 'Dumalaguing', 'Guinoyuran', 'Kalawakan',
            'Kauymonan', 'Liboran', 'Linabo', 'Mambale', 'Mandahican',
            'Patpat', 'Salaan', 'San Roque', 'Santa Cruz', 'Santo Niño'
        ];

        // Sample incident data
        $incidents = [
            // Vehicle-related incidents
            [
                'incident_type' => 'vehicle_vs_vehicle',
                'location' => 'National Highway, near Public Market',
                'barangay' => 'Poblacion',
                'description' => 'Head-on collision between motorcycle and jeepney. Two passengers injured, one in critical condition.',
                'severity_level' => 'severe',
                'vehicles_involved' => 2,
                'casualties_count' => 0,
                'injuries_count' => 2,
                'estimated_damage' => 45000.00,
                'weather_condition' => 'rainy',
                'road_condition' => 'wet',
                'latitude' => 7.1247,
                'longitude' => 125.0623,
                'incident_datetime' => Carbon::now()->subDays(1)->setHour(14)->setMinute(30),
                'status' => 'resolved'
            ],
            [
                'incident_type' => 'vehicle_vs_pedestrian',
                'location' => 'Crossing near Elementary School',
                'barangay' => 'Santo Niño',
                'description' => 'Pedestrian hit by motorcycle while crossing street. Victim sustained minor injuries.',
                'severity_level' => 'moderate',
                'vehicles_involved' => 1,
                'casualties_count' => 0,
                'injuries_count' => 1,
                'estimated_damage' => 5000.00,
                'weather_condition' => 'clear',
                'road_condition' => 'dry',
                'latitude' => 7.1195,
                'longitude' => 125.0589,
                'incident_datetime' => Carbon::now()->subDays(2)->setHour(7)->setMinute(45),
                'status' => 'responding'
            ],
            [
                'incident_type' => 'vehicle_alone',
                'location' => 'Winding road to Kalawakan',
                'barangay' => 'Kalawakan',
                'description' => 'Single vehicle accident. Truck veered off road due to brake failure. Driver injured.',
                'severity_level' => 'moderate',
                'vehicles_involved' => 1,
                'casualties_count' => 0,
                'injuries_count' => 1,
                'estimated_damage' => 75000.00,
                'weather_condition' => 'foggy',
                'road_condition' => 'slippery',
                'latitude' => 7.1108,
                'longitude' => 125.0712,
                'incident_datetime' => Carbon::now()->subDays(3)->setHour(5)->setMinute(20),
                'status' => 'resolved'
            ],
            [
                'incident_type' => 'vehicle_vs_animals',
                'location' => 'Rural road near rice fields',
                'barangay' => 'Aglayan',
                'description' => 'Motorcycle collided with carabao crossing the road. Rider thrown off, sustained injuries.',
                'severity_level' => 'severe',
                'vehicles_involved' => 1,
                'casualties_count' => 0,
                'injuries_count' => 1,
                'estimated_damage' => 15000.00,
                'weather_condition' => 'clear',
                'road_condition' => 'dry',
                'latitude' => 7.1156,
                'longitude' => 125.0734,
                'incident_datetime' => Carbon::now()->subDays(4)->setHour(18)->setMinute(15),
                'status' => 'closed'
            ],

            // Medical emergencies
            [
                'incident_type' => 'maternity',
                'location' => 'Purok 3, near health center',
                'barangay' => 'Poblacion',
                'description' => 'Emergency childbirth case. Mother in labor, complications during delivery.',
                'severity_level' => 'critical',
                'casualties_count' => 0,
                'injuries_count' => 0,
                'hospital_destination' => 'Bukidnon Provincial Hospital',
                'patient_condition' => 'stable',
                'medical_notes' => 'Mother and baby delivered safely. Transported to hospital for observation.',
                'latitude' => 7.1223,
                'longitude' => 125.0601,
                'incident_datetime' => Carbon::now()->subDays(5)->setHour(2)->setMinute(30),
                'status' => 'resolved'
            ],
            [
                'incident_type' => 'transport_to_hospital',
                'location' => 'Remote barangay house',
                'barangay' => 'Guinoyuran',
                'description' => 'Elderly patient with severe chest pain requires immediate hospital transport.',
                'severity_level' => 'critical',
                'casualties_count' => 0,
                'injuries_count' => 0,
                'hospital_destination' => 'Bukidnon Provincial Hospital',
                'patient_condition' => 'critical',
                'medical_notes' => 'Patient experiencing cardiac symptoms. Requires immediate medical attention.',
                'latitude' => 7.1089,
                'longitude' => 125.0645,
                'incident_datetime' => Carbon::now()->subHours(6),
                'status' => 'responding'
            ],
            [
                'incident_type' => 'stabbing_shooting',
                'location' => 'Bar near town plaza',
                'barangay' => 'Poblacion',
                'description' => 'Stabbing incident during altercation. Victim with multiple stab wounds.',
                'severity_level' => 'critical',
                'casualties_count' => 0,
                'injuries_count' => 1,
                'hospital_destination' => 'Bukidnon Provincial Hospital',
                'patient_condition' => 'critical',
                'medical_notes' => 'Multiple stab wounds to chest and abdomen. Emergency surgery required.',
                'latitude' => 7.1234,
                'longitude' => 125.0598,
                'incident_datetime' => Carbon::now()->subDays(7)->setHour(23)->setMinute(45),
                'status' => 'resolved'
            ],

            // Recent incidents (for current activity)
            [
                'incident_type' => 'vehicle_vs_property',
                'location' => 'Main street commercial area',
                'barangay' => 'Poblacion',
                'description' => 'Delivery truck crashed into store front due to mechanical failure.',
                'severity_level' => 'moderate',
                'vehicles_involved' => 1,
                'casualties_count' => 0,
                'injuries_count' => 0,
                'estimated_damage' => 125000.00,
                'weather_condition' => 'clear',
                'road_condition' => 'dry',
                'latitude' => 7.1245,
                'longitude' => 125.0612,
                'incident_datetime' => Carbon::now()->subHours(3),
                'status' => 'pending'
            ],
            [
                'incident_type' => 'maternity',
                'location' => 'Rural house',
                'barangay' => 'Bantuanon',
                'description' => 'Pregnant woman in active labor, unable to reach hospital due to bad weather.',
                'severity_level' => 'severe',
                'casualties_count' => 0,
                'injuries_count' => 0,
                'hospital_destination' => 'Bukidnon Provincial Hospital',
                'patient_condition' => 'stable',
                'medical_notes' => 'Active labor in progress. Midwife on site, transport arranged.',
                'latitude' => 7.1078,
                'longitude' => 125.0723,
                'incident_datetime' => Carbon::now()->subHours(1),
                'status' => 'pending'
            ],
            [
                'incident_type' => 'vehicle_vs_vehicle',
                'location' => 'Highway intersection',
                'barangay' => 'San Roque',
                'description' => 'Minor fender-bender between two motorcycles at intersection.',
                'severity_level' => 'minor',
                'vehicles_involved' => 2,
                'casualties_count' => 0,
                'injuries_count' => 0,
                'estimated_damage' => 8000.00,
                'weather_condition' => 'clear',
                'road_condition' => 'dry',
                'latitude' => 7.1167,
                'longitude' => 125.0578,
                'incident_datetime' => Carbon::now()->subMinutes(45),
                'status' => 'pending'
            ]
        ];

        echo "🚨 Creating incident records...\n";

        foreach ($incidents as $incidentData) {
            // Assign random staff member for some incidents
            if (in_array($incidentData['status'], ['responding', 'resolved', 'closed'])) {
                $incidentData['assigned_staff'] = $staffMembers->random()->id;

                // Assign vehicle for some incidents
                if (rand(0, 1) && $vehicles->count() > 0) {
                    $incidentData['assigned_vehicle'] = $vehicles->random()->id;
                }
            }

            // Set reported_by to admin
            $incidentData['reported_by'] = $admin->id;

            // Create the incident
            $incident = Incident::create($incidentData);

            echo "✅ Created incident #{$incident->incident_number} - {$incident->incident_type}\n";
        }

        echo "\n🎯 Successfully seeded " . count($incidents) . " incident records!\n";
        echo "📊 Distribution:\n";
        echo "   • Vehicle-related: " . collect($incidents)->filter(fn($i) => str_contains($i['incident_type'], 'vehicle'))->count() . "\n";
        echo "   • Medical emergencies: " . collect($incidents)->filter(fn($i) => in_array($i['incident_type'], ['maternity', 'stabbing_shooting', 'transport_to_hospital']))->count() . "\n";
        echo "   • Pending incidents: " . collect($incidents)->filter(fn($i) => $i['status'] === 'pending')->count() . "\n";
        echo "   • With coordinates: " . collect($incidents)->filter(fn($i) => isset($i['latitude']))->count() . "\n";
    }
}
