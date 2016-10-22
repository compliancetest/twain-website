<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrganisationApprovedProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_organisations_approved_products', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->integer('organisation_id');
            $table->integer('product_id');
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
