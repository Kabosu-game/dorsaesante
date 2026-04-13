<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('specialty'); // ex: Généraliste, Cardiologue...
            $table->string('license_number')->unique(); // numéro d'ordre
            $table->string('diploma')->nullable();
            $table->text('bio')->nullable();
            $table->string('structure_name')->nullable(); // cabinet ou clinique
            $table->unsignedBigInteger('health_structure_id')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->boolean('available_teleconsult')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('experience_years')->default(0);
            $table->string('languages')->default('fr'); // ex: fr,ar,en
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
