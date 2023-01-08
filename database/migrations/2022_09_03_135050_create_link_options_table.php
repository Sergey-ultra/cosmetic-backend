<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('category_id');
            $table->json('options');
            $table->json('body')->nullable();
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
        Schema::dropIfExists('link_options');
    }
}
