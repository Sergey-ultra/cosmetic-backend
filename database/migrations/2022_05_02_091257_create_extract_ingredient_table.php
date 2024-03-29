<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtractIngredientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extract_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id');
            $table->foreignId('extract_id');
            $table->timestamps();
            $table->unique(['ingredient_id', 'extract_id'], 'unique_ingredient_extract');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extract_ingredient');
    }
}
