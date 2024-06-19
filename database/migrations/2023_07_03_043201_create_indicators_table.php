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
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug');
            $table->jsonb('title')->nullable();
            $table->jsonb('description')->nullable();
            $table->jsonb('help')->nullable();
            $table->string('data_source')->nullable();
            $table->boolean('published')->default(false);
            $table->string('type')->nullable();
            $table->string('tag', 100)->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->jsonb('data')->default('[]');
            $table->jsonb('layout')->default('{}');
            $table->foreignId('topic_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
