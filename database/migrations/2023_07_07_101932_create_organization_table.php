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
        Schema::create('organization', function (Blueprint $table) {
            $table->id();
            $table->json('name')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('slogan')->nullable();
            $table->json('blurb')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->json('social_media')->default('{"twitter": "", "facebook": "", "instagram": "", "linkedin": ""}');
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization');
    }
};
