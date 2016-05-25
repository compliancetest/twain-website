<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestPlansTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_plans', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->integer('organisation_subscription_id');
            $table->integer('product_id');
            $table->integer('suite_id');
            $table->string('level');
            $table->string('role');
            $table->integer('creator_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('test_plans_excluded_cases', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_plan_id');
            $table->integer('test_case_id');
            $table->text('reason');
            $table->integer('excluded_by_user_id');
            $table->timestamps();
        });

         Schema::table('test_plans_excluded_cases', function($table) {
            $table->foreign('test_plan_id')->references('id')->on('test_plans')->onDelete('cascade');
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
