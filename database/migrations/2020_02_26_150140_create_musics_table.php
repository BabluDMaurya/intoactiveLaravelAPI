<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('title');
            $table->longText('filename');
            $table->integer('artist_id')->nullable();
            $table->integer('genre_id')->nullable();
            $table->integer('language_id')->nullable();
            $table->string('station_id')->nullable();
            $table->string('tags_id')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('is_delete')->nullable();
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
        Schema::dropIfExists('musics');
    }
}
