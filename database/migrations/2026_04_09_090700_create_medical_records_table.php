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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // consultation, lab_result, prescription, imaging, vaccination
            $table->string('title');
            $table->text('content')->nullable(); // diagnostic, observations
            $table->json('attachments')->nullable(); // fichiers joints
            $table->date('record_date');
            $table->string('diagnosis')->nullable(); // diagnostic principal
            $table->json('medications')->nullable(); // médicaments prescrits
            $table->text('follow_up_notes')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
