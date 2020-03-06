<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNutritionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrition', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uid');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('instruction')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('type')->default(1)->comment('1=breakfast , 2=brunch , 3 =null');
            $table->longText('ingredients')->nullable();
            $table->integer('preparation_time')->nullable();
            $table->integer('bevrage_type')->nullable()->comment('1=water, 2=coke');
            $table->integer('bevrage_quantity')->nullable();
            $table->integer('bevrage_option')->nullable();
            $table->integer('bevrage_unit')->nullable();
            $table->boolean('bevrage_inclusion')->default(0)->comment('include bevrage in this meal');
            $table->double('total_calorie')->default(0);
            $table->double('total_carbohydrate')->default(0);
            $table->double('total_protein')->default(0);
            $table->double('total_fat')->default(0);
            $table->double('total_sugar')->default(0);
            $table->double('total_cholestrol')->default(0);            
            
            $table->boolean('is_deleted')->default(0)->comment('0=not delete, 1=deleted');  
           
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
        Schema::dropIfExists('nutrition');
    }
}
