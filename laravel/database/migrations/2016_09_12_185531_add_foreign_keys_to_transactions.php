<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\WpOptions::create([
            'option_name' => 'transactions_purge_period',
            'option_value' => '30',
        ]);
        \Illuminate\Support\Facades\DB::statement("SET foreign_key_checks = 0;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transaction_change_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        Schema::table('transactions_logs', function ($table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
        \Illuminate\Support\Facades\DB::statement("SET foreign_key_checks = 1;");
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
