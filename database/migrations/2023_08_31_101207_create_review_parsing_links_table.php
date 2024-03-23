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
        Schema::create('review_parsing_links', function (Blueprint $table) {
            $table->id();
            $table->string('link')->unique();
            $table->longText('body')->nullable();
            $table->json('content')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->foreignId('category_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_parsing_links');
    }
};
