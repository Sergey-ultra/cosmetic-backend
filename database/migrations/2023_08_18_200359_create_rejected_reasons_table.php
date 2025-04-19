<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('rejected_reasons', function (Blueprint $table) {
            $table->id();
            $table->text('reason');
            $table->timestamps();
        });

//        DB::table('rejected_reasons')->insert([
//            ['reason' => 'Количество знаков должно быть 5000 или больше'],
//            ['reason' => 'Количество фотографий должно быть 3 или больше'],
//        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rejected_reasons');
    }
};
