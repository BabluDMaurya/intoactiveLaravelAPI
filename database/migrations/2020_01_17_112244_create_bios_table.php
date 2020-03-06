<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('bios')){
            Schema::create('bios', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id')->unsigned();
                $table->string('display_name')->nullable();
                $table->longtext('about_me')->nullable();
                $table->longtext('hometown')->nullable();
                $table->string('profile_pic')->nullable();
                $table->string('main_profile_pic')->nullable();
                $table->string('profile_background_image')->nullable();
                $table->integer('specialities_id')->nullable();
                $table->string('secondary_specialities_ids')->nullable();
                $table->string('languages_id')->nullable();
                $table->string('birth_year')->nullable();
                $table->string('country_id')->nullable();
                $table->string('state_id')->nullable();
                $table->string('city_id')->nullable();
                $table->integer('user_type')->unsigned()->comment('0 = User , 1 = Consultant');
                $table->string('gender')->comment('1 = male , 2 = female');
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
        Schema::dropIfExists('bios');
    }
}
