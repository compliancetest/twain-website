<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToTransactions2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('transactions_logs', function ($table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('transactions_logs', function ($table) {
            $table->dropForeign('transactions_logs_transaction_id_foreign');
        });
        Schema::enableForeignKeyConstraints();
    }
}
