<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create 3 users for the MDRRMO system

        // 1. Main Admin User (use your email here)
        $adminEmail = 'dongzralph@gmail.com'; // <-- CHANGE THIS TO YOUR REAL EMAIL

        $admin = User::create([
            'first_name' => 'MDRRMO',
            'last_name' => 'Administrator',
            'email' => $adminEmail,
            'role' => 'admin',
            'password' => Hash::make('Admin@123'),
            'phone_number' => '+63912-345-6789',
            'municipality' => 'Maramag',
            'position' => 'MDRRMO Chief Administrator',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. MDRRMO Staff Member 1
        $staff1 = User::create([
            'first_name' => 'Juan',
            'last_name' => 'Santos',
            'email' => 'juan.santos@mdrrmo.maramag.gov.ph',
            'role' => 'mdrrmo_staff',
            'password' => Hash::make('Staff@123'),
            'phone_number' => '+63923-456-7890',
            'municipality' => 'Maramag',
            'position' => 'Emergency Response Officer',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. MDRRMO Staff Member 2
        $staff2 = User::create([
            'first_name' => 'Maria',
            'last_name' => 'Cruz',
            'email' => 'maria.cruz@mdrrmo.maramag.gov.ph',
            'role' => 'mdrrmo_staff',
            'password' => Hash::make('Staff@123'),
            'phone_number' => '+63934-567-8901',
            'municipality' => 'Maramag',
            'position' => 'Disaster Risk Assessment Specialist',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Display created users info
        $this->command->info('✅ Successfully created 3 MDRRMO users!');
        $this->command->line('');

        $this->command->info('👤 ADMIN USER:');
        $this->command->info('📧 Email: ' . $admin->email);
        $this->command->info('🔑 Password: Admin@123');
        $this->command->info('👨‍💼 Role: Administrator');
        $this->command->line('');

        $this->command->info('👤 STAFF USER 1:');
        $this->command->info('📧 Email: ' . $staff1->email);
        $this->command->info('🔑 Password: Staff@123');
        $this->command->info('👨‍💼 Role: ' . $staff1->position);
        $this->command->line('');

        $this->command->info('👤 STAFF USER 2:');
        $this->command->info('📧 Email: ' . $staff2->email);
        $this->command->info('🔑 Password: Staff@123');
        $this->command->info('👨‍💼 Role: ' . $staff2->position);
        $this->command->line('');

        $this->command->warn('⚠️  Please change passwords after first login!');
        $this->command->warn('⚠️  Make sure to update the admin email before running this seeder!');
    }
}
