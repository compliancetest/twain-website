<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTransactions1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\App\WpOptions::where('option_name', 'transactions_purge_period')->first()) {
            \App\WpOptions::create([
                'option_name' => 'transactions_purge_period',
                'option_value' => '30',
            ]);
        }
        \Illuminate\Support\Facades\DB::statement("SET foreign_key_checks = 0;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transaction_change_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        Schema::table('transactions_logs', function ($table) {
            $keyExists = \Illuminate\Support\Facades\DB::select(
                DB::raw(
                    'SHOW KEYS
                    FROM transactions_logs
                    WHERE Key_name=\'transaction_id\''
                )
            );
            if (!$keyExists) {
                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            }
        });
        \Illuminate\Support\Facades\DB::statement("SET foreign_key_checks = 1;");

        $periodOption = \Illuminate\Support\Facades\DB::table('wp_options')->where('option_name', 'transactions_purge_period')->first();
        $days = $periodOption ? $periodOption->option_value : 30;
        \Illuminate\Support\Facades\DB::table('transactions')->where('audit_record', false)->where('created_at', '<=', \Carbon\Carbon::now()->subDays($days))->delete();
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
