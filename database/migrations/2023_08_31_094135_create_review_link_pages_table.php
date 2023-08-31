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
        Schema::create('review_link_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('review_link_option_id');
            $table->tinyInteger('page_number');
            $table->json('body')->nullable();
            $table->timestamps();
            $table->index('review_link_option_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_link_pages');
    }
};
