<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExplanationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('transaction_explanation_logs', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('transaction_id');
            $table->integer('user_id');
            $table->boolean('is_support');
            $table->text('message');
            $table->timestamps();
        });

         Schema::table('transaction_explanation_logs', function ($table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transaction_explanation_logs');
    }
}
