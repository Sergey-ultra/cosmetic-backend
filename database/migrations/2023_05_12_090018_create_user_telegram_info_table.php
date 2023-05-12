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
        Schema::create('user_telegram_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('hash', 36)->nullable();
            $table->string('telegram_user_name')->nullable();
            $table->unsignedBigInteger('telegram_user_id')->nullable();
            $table->unsignedSmallInteger('unsubscribe_code')->nullable();
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
        Schema::dropIfExists('user_telegram_info');
    }
};
