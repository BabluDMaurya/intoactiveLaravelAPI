<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('user_name')->unique();
                $table->string('email')->unique();
                $table->string('insta_id')->nullable();
                $table->string('insta_token')->nullable();
                $table->string('password');
                $table->string('birth_year')->nullable();
                $table->tinyInteger('gender')->comment('1 = male , 2= female');
                $table->string('ip_address')->nullable();
                $table->string('location')->nullable();
                $table->string('register_timezone')->nullable();
                $table->string('otp_code')->nullable();
                $table->tinyInteger('user_type')->default(0)->comment('0 = user , 1 = consultant ');
                $table->tinyInteger('is_verified')->default(0)->comment('0 = not verified, 1 = verified');
                $table->tinyInteger('is_active')->default(1)->comment('0 = inactive, 1 = active');
                $table->tinyInteger('is_delete')->default(0)->comment('0 = not delete, 1 = delete');
                $table->string('last_login_time')->nullable();
                $table->string('last_login_address')->nullable();
                $table->string('last_login_ip')->nullable();
                $table->string('timezone')->nullable()->default('America/New_York');
                $table->string('inst_token_id')->nullable();            
                $table->timestamps();
            });
        }
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
