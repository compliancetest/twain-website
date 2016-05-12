<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommunitiesSurveysResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_surveys_results', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('community_id');
            $table->text('survey_id');
            $table->integer('author_id')->unsigned();
            $table->text('link');

            $table->timestamps();
        });

        Schema::table('community_surveys_results', function($table) {
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
        //
    }
}
