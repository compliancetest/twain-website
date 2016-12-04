<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToClaimTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("delete FROM `claim_transactions` WHERE claim_id not in(SELECT id FROM claims)");
        Schema::table('claim_transactions', function ($table) {
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
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
