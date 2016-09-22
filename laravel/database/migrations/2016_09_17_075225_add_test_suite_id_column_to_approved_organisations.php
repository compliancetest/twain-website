<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestSuiteIdColumnToApprovedOrganisations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::rename('communities_approved_organisations', 'communities_organisations_approved_test_suites');
         Schema::table('communities_organisations_approved_test_suites', function(Blueprint $table) {
            $table->integer('test_suite_id');
        });
        \App\CommunityOrganisationsApprovedTestSuites::where('test_suite_id', 0)->delete();
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
