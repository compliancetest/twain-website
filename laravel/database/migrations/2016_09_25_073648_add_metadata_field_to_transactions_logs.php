<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetadataFieldToTransactionsLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_logs', function(Blueprint $table) {
            $table->text('scan_results_meta')->after('scan_results');
        });
        Schema::table('transactions', function(Blueprint $table) {
            $table->text('execution_config');
        });
        App\WpOptions::create([
            'option_name' => 'server_validation',
            'option_value' => 'yes',
        ]);
        App\WpOptions::create([
            'option_name' => 'image_viewer',
            'option_value' => 'yes',
        ]);
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
