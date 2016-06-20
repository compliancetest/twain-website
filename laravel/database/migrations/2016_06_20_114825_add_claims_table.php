<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_plan_id');
            $table->integer('organisation_id');
            $table->integer('product_id');
            $table->integer('test_suite_id');
            $table->integer('creator_id');
            $table->string('conformance_level');
            $table->string('role');
            $table->string('status');
            $table->string('audit');
            $table->boolean('has_exclusions');
            $table->timestamps();
        });

         Schema::table('claims', function($table) {
            $table->foreign('test_plan_id')->references('id')->on('test_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
