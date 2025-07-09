<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for MDRRMO system.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);

        $this->command->info('🏥 MDRRMO Database seeded successfully!');
        $this->command->info('🚀 You can now login as admin and register MDRRMO staff members.');
    }
}
