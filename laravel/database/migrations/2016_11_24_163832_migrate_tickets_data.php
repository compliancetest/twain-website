<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateTicketsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `wp_tickets` CHANGE  `test_suite_id`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");

        $tickets = \Illuminate\Support\Facades\DB::table('wp_tickets')->get();
        foreach ($tickets as $ticket) {
            $testSuite = \App\LaravelTestSuite::where(['wp_id' => $ticket->suite_minor_family_mark])->first();
            if ($testSuite && !empty($ticket->suite_minor_family_mark)) {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `wp_tickets` SET  `suite_minor_family_mark`  = '{$testSuite->minor_family_mark}' WHERE suite_minor_family_mark = {$ticket->suite_minor_family_mark} AND id = {$ticket->id}");
            } else {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `wp_tickets` SET  `suite_minor_family_mark`  = '' WHERE suite_minor_family_mark = {$ticket->suite_minor_family_mark} AND id = {$ticket->id}");
            }
        }
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
