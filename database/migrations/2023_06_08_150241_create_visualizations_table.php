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
            $table->jsonb('title')->nullable();
            $table->jsonb('description')->nullable();
            $table->boolean('published')->default(false);
            $table->unsignedBigInteger('topic_id')->nullable();

            $table->string('type');
            $table->jsonb('data_params')->default('{}');
            $table->jsonb('options')->default('{}');
            $table->string('livewire_component');

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
