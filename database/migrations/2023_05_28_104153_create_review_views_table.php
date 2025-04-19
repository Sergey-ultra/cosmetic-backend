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
        Schema::create('review_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id');
            $table->string('ip_address', 40);
            $table->unsignedTinyInteger('handled')->default(0);
            $table->timestamps();
            $table->unique(['review_id', 'ip_address', 'handled']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_views');
    }
};
