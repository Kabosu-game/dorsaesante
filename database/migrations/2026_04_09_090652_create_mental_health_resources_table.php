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
        Schema::create('mental_health_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type'); // article, video, audio, quiz, exercise
            $table->string('category'); // stress, depression, anxiety, sleep, addiction
            $table->string('thumbnail')->nullable();
            $table->string('media_url')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('requires_professional')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('duration_minutes')->nullable(); // durée de lecture/écoute
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_resources');
    }
};
