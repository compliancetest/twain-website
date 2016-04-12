<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function(Blueprint $table) {
            $table->uuid('id');
            $table->integer('product_id');
            $table->string('execution_id');
            $table->integer('test_suite_id');
            $table->integer('customer_id');
            $table->integer('test_case_id');
            $table->integer('subscription_id');
            $table->boolean('audit_record');
            $table->uuid('test_outcome_status_id');
            $table->integer('organisation_id');
            $table->timestamps();
			$table->primary('id');
        });

        Schema::create('transactions_logs', function(Blueprint $table) {
            $table->uuid('id');
            $table->uuid('transaction_id');
            $table->string('execution_id');
            $table->string('test_step');
            $table->string('from');
            $table->string('to');
            $table->string('operation_triplet');
            $table->string('return_code');
            $table->integer('session_state');
            $table->string('message_data');
            $table->string('twain_session_id');
            $table->text('screen_captures');
            $table->text('scan_results');
            $table->boolean('status');
            $table->timestamps();
			$table->primary('id');
        });

        Schema::create('test_outcome_statuses', function(Blueprint $table) {
            $table->uuid('id');
            $table->string('name');
			$table->primary('id');
        });

//         Schema::table('transactions', function($table) {
//            $table->foreign('test_outcome_status_id')->references('id')->on('test_outcome_statuses');
//        });
//        Schema::table('transactions_logs', function($table) {
//            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
//        });
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
