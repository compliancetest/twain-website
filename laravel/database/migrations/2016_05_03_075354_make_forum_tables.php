<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeForumTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_forum_threads', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('community_id');
            $table->integer('author_id')->unsigned();
            $table->string('title');
            $table->string('slug');
            $table->string('content');

            $table->timestamps();
        });

        Schema::create('community_forum_posts', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('thread_id');
            $table->integer('author_id')->unsigned();
            $table->text('content');

            $table->timestamps();
        });

        Schema::create('community_forum_threads_read', function (Blueprint $table) {
            $table->uuid('thread_id');
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('community_forum_threads', function($table) {
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
        });

        Schema::table('community_forum_posts', function($table) {
            $table->foreign('thread_id')->references('id')->on('community_forum_threads')->onDelete('cascade');
        });

        Schema::table('community_forum_threads_read', function($table) {
            $table->foreign('thread_id')->references('id')->on('community_forum_threads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
