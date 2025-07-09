<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create MDRRMO Admin user
        User::create([
            'first_name' => 'MDRRMO',
            'last_name' => 'Administrator',
            'email' => 'admin@mdrrmo-maramag.gov.ph',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'phone_number' => '+63912-345-6789',
            'municipality' => 'Maramag',
            'position' => 'MDRRMO Chief',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create MDRRMO Staff users
        User::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@mdrrmo-maramag.gov.ph',
            'role' => 'mdrrmo_staff',
            'password' => Hash::make('staff123'),
            'phone_number' => '+63912-345-6790',
            'municipality' => 'Maramag',
            'position' => 'Emergency Response Officer',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@mdrrmo-maramag.gov.ph',
            'role' => 'mdrrmo_staff',
            'password' => Hash::make('staff123'),
            'phone_number' => '+63912-345-6791',
            'municipality' => 'Maramag',
            'position' => 'Medical Response Coordinator',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Pedro',
            'last_name' => 'Reyes',
            'email' => 'pedro.reyes@mdrrmo-maramag.gov.ph',
            'role' => 'mdrrmo_staff',
            'password' => Hash::make('staff123'),
            'phone_number' => '+63912-345-6792',
            'municipality' => 'Maramag',
            'position' => 'Vehicle Coordinator',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create test user for development
        User::create([
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'email' => 'test@mdrrmo.local',
            'role' => 'mdrrmo_staff',
            'password' => Hash::make('password123'),
            'phone_number' => '+63912-345-6793',
            'municipality' => 'Maramag',
            'position' => 'Test Officer',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
