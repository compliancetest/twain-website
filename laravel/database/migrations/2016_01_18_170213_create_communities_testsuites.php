<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunitiesTestsuites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_testsuites', function (Blueprint $table) {
            $table->primary('id');
            $table->string('id', 36);
            $table->string('community_id', 36);
            $table->integer('testsuite_id')->unsigned();
        });

        Schema::table('communities_testsuites', function($table) {
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
        Schema::drop('communities_testsuites');
    }
}
