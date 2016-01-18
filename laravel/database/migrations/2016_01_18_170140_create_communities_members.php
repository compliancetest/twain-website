<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunitiesMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('community_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('user_title');
            $table->boolean('is_admin');
            $table->boolean('is_mod');
            $table->boolean('is_banned');
            $table->boolean('invite_sent');
            $table->timestamps();
        });

        Schema::table('communities_members', function($table) {
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
        Schema::drop('communities_members');
    }
}
