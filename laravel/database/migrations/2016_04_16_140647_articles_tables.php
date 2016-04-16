<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArticlesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_articles', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->integer('creator_id');
            $table->string('title');
            $table->text('content');
            $table->string('visibility');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('community_articles_attachments', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('article_id');
            $table->string('filename');
            $table->text('location');
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
        //
    }
}
