<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number', 50)->unique();
            $table->string('vehicle_type');
            $table->string('make_model', 100);
            $table->integer('year');
            $table->string('plate_number', 20)->unique();
            $table->string('status')->default('available');
            $table->string('municipality', 100)->default('Maramag');
            $table->integer('capacity');
            $table->decimal('fuel_capacity', 8, 2);
            $table->decimal('current_fuel', 8, 2)->default(0);
            $table->integer('odometer_reading')->default(0);
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance_due')->nullable();
            $table->text('equipment_list')->nullable();
            $table->boolean('is_operational')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index('vehicle_type');
            $table->index('status');
            $table->index('municipality');
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
