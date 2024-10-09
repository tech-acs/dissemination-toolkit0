<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visualizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug');
            $table->jsonb('title')->nullable();
            $table->jsonb('description')->nullable();
            $table->boolean('published')->default(false);
            $table->foreignId('user_id');
            $table->boolean('is_filterable')->default(false);

            $table->string('livewire_component');
            $table->jsonb('data_params')->default('{}');

            $table->jsonb('data')->default('[]');
            $table->jsonb('layout')->default('{}');
            $table->text('thumbnail')->nullable();

            $table->jsonb('options')->default('{}');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visualizations');
    }
};
