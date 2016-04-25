<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateTestOutcomeStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `test_outcome_statuses` ADD `code` VARCHAR( 20 ) NOT NULL AFTER `name` ;');

        \App\TestOutcomeStatus::create(['name' => 'Passed', 'code' => 'PASS']);
        \App\TestOutcomeStatus::create(['name' => 'Failed', 'code' => 'FAIL']);
        \App\TestOutcomeStatus::create(['name' => 'Not performed', 'code' => 'NOT_PERFORMED']);
        \App\TestOutcomeStatus::create(['name' => 'Warning', 'code' => 'WARNING']);
        \App\TestOutcomeStatus::create(['name' => 'In Progress', 'code' => 'IN_PROGRESS']);
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
