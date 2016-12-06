<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsLatestVersionFlagToTestSuites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_suites', function ($table) {
            $table->boolean('is_latest_version');
        });
        foreach (\App\LaravelTestSuite::groupBy('minor_family_mark')->get() as $testSuite){
            $latestVersion = \App\LaravelTestSuite::getLatestSuiteForMinorFamilyMark($testSuite->minor_family_mark);
            \Illuminate\Support\Facades\DB::statement("UPDATE `test_suites` SET is_latest_version = 1 WHERE id = '".$latestVersion->id."'");
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
