<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->jsonb('title');
            $table->string('slug');
            $table->jsonb('description')->nullable();
            $table->text('html')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('featured')->default(false);
            $table->string('featured_image')->nullable();
            //$table->unsignedTinyInteger('rank')->nullable();
            $table->foreignId('user_id');
            $table->boolean('is_filterable')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
};
