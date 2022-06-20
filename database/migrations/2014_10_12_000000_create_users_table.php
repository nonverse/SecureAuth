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
            $table->uuid();
            $table->string('username');
            $table->string('name_first');
            $table->string('name_last');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('admin');
            $table->boolean('use_totp');
            $table->string('totp_secret');
            $table->string('violations');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('totp_authenticated_at')->nullable();
            $table->timestamp('violation_ends_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
