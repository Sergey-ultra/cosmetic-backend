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
            $table->enum('rating',[1,2,3,4,5]);
            $table->foreignId('sku_id')->index();
            $table->foreignId('user_id')->index();
            $table->string('title', 256);
            $table->json('body')->nullable();
            $table->text('plus')->nullable();
            $table->text('minus')->nullable();
            $table->tinyInteger('anonymously')->default(0);
            $table->tinyInteger('is_recommend')->default(0);
            $table->json('images')->nullable();
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
