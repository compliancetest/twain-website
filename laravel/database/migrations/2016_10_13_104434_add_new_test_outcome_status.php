<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewTestOutcomeStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_change_logs', function(Blueprint $table) {
            $table->integer('deleted_by');
        });

        \App\TestOutcomeStatus::create([
            'name' => 'Deleted',
            'code' => 'DELETED',
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
