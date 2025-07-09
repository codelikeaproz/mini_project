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
        Schema::create('victims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->text('address');
            $table->string('involvement_type');
            $table->string('injury_status');
            $table->string('hospital_referred')->nullable();
            $table->timestamp('hospital_arrival_time')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('transport_method')->nullable();
            $table->string('vehicle_type', 50)->nullable();
            $table->string('vehicle_plate_number', 20)->nullable();
            $table->boolean('wearing_helmet')->nullable();
            $table->boolean('wearing_seatbelt')->nullable();
            $table->string('license_status')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('incident_id');
            $table->index('injury_status');
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        Schema::dropIfExists('victims');
    }
};
