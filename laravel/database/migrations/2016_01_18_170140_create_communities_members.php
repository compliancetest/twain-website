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
            $table->primary('id');
            $table->string('id', 36);
            $table->string('community_id');
            $table->integer('user_id')->unsigned();
            $table->string('user_title');
            $table->boolean('is_admin');
            $table->boolean('is_mod');
            $table->boolean('is_banned');
            $table->boolean('invite_sent');
            $table->integer('inviter_id');
            $table->boolean('is_confirmed');
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
