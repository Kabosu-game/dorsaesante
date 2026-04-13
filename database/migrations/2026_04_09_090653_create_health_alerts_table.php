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
        Schema::create('health_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('level', ['info', 'warning', 'danger', 'critical'])->default('info');
            $table->string('type'); // epidemic, campaign, environmental, emergency, vaccination
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete(); // null = nationale
            $table->boolean('is_active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->json('target_roles')->nullable(); // null = tous les utilisateurs
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_alerts');
    }
};
