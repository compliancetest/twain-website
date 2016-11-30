<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateTestCases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $testCases = \App\Post::where('post_type', 'test-case')->get();
        foreach($testCases as $testCase){
            $isHidden = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'hide_case', 'meta_value' => '1'])->first();
            if($isHidden){
                \App\LaravelTestCase::where('wp_id', $testCase->ID)->update(['status' => 'Obsolete']);
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
