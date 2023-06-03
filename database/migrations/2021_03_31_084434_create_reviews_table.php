<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sku_rating_id')->index();
            $table->string('title', 256);
            $table->text('body')->nullable();
            $table->text('plus')->nullable();
            $table->text('minus')->nullable();
            $table->tinyInteger('anonymously')->default(0);
            $table->tinyInteger('is_recommend')->default(0);
            $table->text('images')->nullable();
            $table->string('status')->default('moderated');
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
        Schema::dropIfExists('reviews');
    }
}
