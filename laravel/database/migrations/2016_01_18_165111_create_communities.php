<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->integer('creator_id');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('articles_status');
            $table->string('visibility_status');
            $table->string('slug')->unique();
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
        Schema::drop('communities');
    }
}
