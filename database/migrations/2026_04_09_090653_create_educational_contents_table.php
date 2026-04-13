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
        Schema::create('educational_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type'); // article, video, infographic, podcast
            $table->string('category'); // vaccination, prevention, nutrition, hygiene, first_aid, chronic_diseases
            $table->string('thumbnail')->nullable();
            $table->string('media_url')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('views_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->string('tags')->nullable(); // ex: "paludisme,prévention,moustique"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_contents');
    }
};
