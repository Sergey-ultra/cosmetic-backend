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
        Schema::create('user_balance_accruals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('review_id');
            $table->unsignedInteger('accrual');
            $table->enum('type', ['view', 'bonus']);
            $table->date('date');
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
        Schema::dropIfExists('user_balance_accruals');
    }
};
