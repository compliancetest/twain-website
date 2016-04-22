<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArticlesAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('community_articles', function($table) {
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
        });
        Schema::table('community_articles_attachments', function($table) {
            $table->foreign('article_id')->references('id')->on('community_articles')->onDelete('cascade');
        });
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE community_articles DROP FOREIGN KEY community_articles_community_id_foreign');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE community_articles_attachments DROP FOREIGN KEY community_articles_attachments_article_id_foreign');

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
