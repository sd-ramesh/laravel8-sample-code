<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('business_name');
            $table->string('trading_as')->default(null)->nullable();
            $table->string('abn', 13)->nullable()->unique();
            $table->longText('logo')->default(null)->nullable();
            $table->longText('qrcode')->default(null)->nullable(); 
            $table->string('waiting_message')->nullable(); 
            $table->string('ready_message')->nullable();  
            $table->boolean('sms_notification')->default(0);
            $table->boolean('push_notification')->default(0);
            $table->boolean('email_notification')->default(0);
            $table->boolean('reminder_prompt')->default(1);
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
        Schema::dropIfExists('vendors');
    }
}
