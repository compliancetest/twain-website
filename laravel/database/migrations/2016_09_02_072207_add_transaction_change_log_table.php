<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionChangeLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_change_logs', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->string('execution_id');
            $table->integer('user_id');
            $table->uuid('test_outcome_status_id');
            $table->boolean('changed_by_server_validation');
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
        Schema::drop('transaction_change_logs');
    }
}
