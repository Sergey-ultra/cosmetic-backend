<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkuStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_store', function (Blueprint $table) {
            $table->foreignId('sku_id');
            $table->foreignId('store_id');
            $table->foreignId('link_id');
            $table->unsignedInteger('fails_count')->default(0);
            $table->integer('price')->nullable();
            $table->timestamps();
            $table->primary(['sku_id', 'store_id', 'link_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price');
    }
}
