<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExecutionModeToAllTestCases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach( \App\Post::where(['post_type' => 'test-case'])->get() as $testCase){
            $testCase->postmeta()->updateOrCreate(['meta_key' => 'executionMode'], ['meta_value' => 'Auto']);
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
