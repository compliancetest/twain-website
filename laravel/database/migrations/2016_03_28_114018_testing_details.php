<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TestingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testing_details', function (Blueprint $table) {

            $table->uuid('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('test_suite_id');
            $table->unsignedInteger('test_case_id');
            $table->unsignedInteger('product_id');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_running');

            $table->primary('id');
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
