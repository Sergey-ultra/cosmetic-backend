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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedInteger('referral_balance')->default(0);
            $table->string('service')->nullable();
            $table->string('service_user_id')->nullable();
            $table->unsignedTinyInteger('role_id');
            $table->string('ref', 12)->nullable();
            $table->unsignedBigInteger('referral_owner')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->unique(['email', 'service']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
