<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_options', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('value');
            $table->text('description');
            $table->timestamps();
            $table->unique('key', 'key_unique');
        });

        DB::table('configuration_options')->insert(['key' => 'week_status', 'value' => 'true']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configiration_options');
    }
}
