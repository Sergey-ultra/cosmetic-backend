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
        Schema::create('sku_videos', function (Blueprint $table) {
            $table->id();
            $table->text('video');
            $table->text('thumbnail');
            $table->string('description');
            $table->unsignedInteger('sku_id')->index();
            $table->unsignedInteger('user_id')->nullable()->index();
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
        Schema::dropIfExists('sku_videos');
    }
};
