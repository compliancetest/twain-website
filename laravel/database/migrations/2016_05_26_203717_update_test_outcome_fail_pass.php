<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTestOutcomeFailPass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('UPDATE test_outcome_statuses SET name = "Fail" WHERE code="FAIL"');
        \Illuminate\Support\Facades\DB::statement('UPDATE test_outcome_statuses SET name = "Pass" WHERE code="PASS"');
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
