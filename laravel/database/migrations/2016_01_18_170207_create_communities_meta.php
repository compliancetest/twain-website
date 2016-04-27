<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunitiesMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_meta', function (Blueprint $table) {
            $table->primary('id');
            $table->string('id', 36);
            $table->string('community_id', 36);
            $table->string('meta_key');
            $table->text('meta_value');
        });

        Schema::table('communities_meta', function($table) {
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
        Schema::drop('communities_meta');
    }
}
