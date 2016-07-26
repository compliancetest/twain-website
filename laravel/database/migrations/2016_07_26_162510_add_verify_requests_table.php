<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_requests', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_plan_id');
            $table->integer('test_suite_id');
            $table->integer('product_id');
            $table->integer('requestor_id')->unsigned();
            $table->integer('assignee_id')->unsigned()->default(0);
            $table->text('transactions');
            $table->string('status')->default('New');
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
        Schema::drop('verify_requests');
    }
}
