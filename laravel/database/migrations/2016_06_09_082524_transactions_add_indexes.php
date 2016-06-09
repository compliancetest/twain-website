<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionsAddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions` ADD INDEX `subscription_id` (`subscription_id`)");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions` ADD INDEX `created_at` (`created_at`)");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions_logs` ADD INDEX `transaction_id` (`transaction_id`)");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions_logs` ADD INDEX `execution_id` (`execution_id`)");

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
