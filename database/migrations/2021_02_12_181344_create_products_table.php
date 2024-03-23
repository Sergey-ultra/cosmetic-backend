<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('category_id');
            $table->foreignId('brand_id');
            $table->foreignId('user_id')->nullable();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->text('application')->nullable();
            $table->string('purpose')->nullable();
            $table->string('effect')->nullable();
            $table->string('type_of_skin')->nullable();
            $table->string('age')->nullable();
            $table->timestamps();
            $table->unique(['brand_id', 'name']);
        });
        //DB::statement('ALTER TABLE products ADD FULLTEXT search(name, name_en, description, application)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
