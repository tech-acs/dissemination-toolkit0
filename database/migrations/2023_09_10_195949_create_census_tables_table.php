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
        Schema::create('census_tables', function (Blueprint $table) {
            $table->id();
            $table->jsonb('title');
            $table->jsonb('description');
            $table->string('dataset_type')->default('table');
            $table->string('producer');
            $table->string('publisher');
            $table->date('published_date');
            $table->string('data_source');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('comment')->nullable();
            $table->boolean('published')->default(false);
            $table->foreignId('user_id');
            $table->integer('view_count')->default(1);
            $table->integer('download_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('census_tables');
    }
};
