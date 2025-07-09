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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number', 50)->unique();
            $table->string('incident_type');
            $table->string('location');
            $table->string('municipality', 100)->default('Maramag');
            $table->string('barangay', 100);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('incident_datetime');
            $table->text('description');
            $table->string('severity_level');
            $table->string('status')->default('pending');
            $table->integer('vehicles_involved')->default(0);
            $table->integer('casualties_count')->default(0);
            $table->integer('injuries_count')->default(0);
            $table->decimal('estimated_damage', 15, 2)->nullable();
            $table->string('hospital_destination')->nullable();
            $table->string('patient_condition')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('weather_condition')->nullable();
            $table->string('road_condition')->nullable();
            $table->unsignedBigInteger('assigned_vehicle')->nullable();
            $table->json('additional_details')->nullable();
            $table->foreignId('reported_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('assigned_staff')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index('incident_type');
            $table->index('municipality');
            $table->index('incident_datetime');
            $table->index('status');
            $table->index(['latitude', 'longitude'], 'idx_incidents_coordinates');
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
