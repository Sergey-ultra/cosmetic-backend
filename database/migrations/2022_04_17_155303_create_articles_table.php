<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('preview');
            $table->text('body');
            $table->text('image')->nullable();
            $table->unsignedInteger('user_id');
            $table->string('status', 40)->default('moderated');
            $table->unsignedTinyInteger('article_category_id');
            $table->timestamps();
            $table->index('user_id');
            $table->index('article_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
