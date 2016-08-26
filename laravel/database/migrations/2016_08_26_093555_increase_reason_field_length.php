<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseReasonFieldLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `transactions` CHANGE  `reason`  `reason` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `transactions_logs` CHANGE  `reason`  `reason` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;");
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
