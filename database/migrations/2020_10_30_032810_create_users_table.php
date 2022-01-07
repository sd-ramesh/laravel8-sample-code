<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('country_code')->nullable()->default(null);
            $table->string('phone_number')->nullable()->default(null);      
            $table->longText('profile_image')->default(null)->nullable();
            $table->string('username')->nullable()->default(null);
            $table->string('password')->nullable();
            $table->enum('role', ['customer', 'vendor', 'administrator'])->nullable();
            $table->enum('device_type',['ios', 'android'])->nullable();
            $table->text('device_token')->nullable();
            $table->text('last_login_data')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('phone_verified')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
}
