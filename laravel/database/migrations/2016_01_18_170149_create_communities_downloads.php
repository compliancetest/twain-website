<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunitiesDownloads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_downloads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('community_id')->unsigned();
            $table->string('title');
            $table->text('description');
            $table->text('license');
            $table->string('location');
            $table->integer('size');
            $table->string('version');
            $table->text('version_description');
            $table->string('download_file_name');
            $table->string('token');
            $table->timestamps();
        });

        Schema::table('communities_downloads', function($table) {
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('communities_downloads');
    }
}
