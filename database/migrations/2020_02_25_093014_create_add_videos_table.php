<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('post_id');
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('video_path')->nullable();            
            $table->longText('thumb_path')->nullable();
            $table->string('duration')->nullable();
            $table->string('views')->nullable();
            $table->integer('savedvideo')->nullable();            
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
        Schema::dropIfExists('add_videos');
    }
}
