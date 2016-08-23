<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommunityApprovedOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_approved_organisations', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->integer('organisation_id');
            $table->integer('approved_by')->unsigned();
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
