<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSuiteProductsCasesReferencesInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `claims` CHANGE  `product_id`  `product_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `claims` CHANGE  `test_suite_id`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");

        $claims = \App\Claim::all();
        foreach ($claims as $claim) {
            $product = \App\Product::where(['wp_id' => $claim->product_id])->first();
            $testSuite = \App\LaravelTestSuite::where(['wp_id' => $claim->suite_minor_family_mark])->first();
            if ($product && $testSuite) {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `claims` SET  `product_id`  = '{$product->id}' WHERE product_id = {$claim->product_id}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `claims` SET  `suite_minor_family_mark`  = '{$testSuite->id}' WHERE suite_minor_family_mark = {$claim->suite_minor_family_mark}");
            }
        }

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `transactions` CHANGE  `product_id`  `product_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `transactions` CHANGE  `test_suite_id`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `transactions` CHANGE  `test_case_id`  `test_case_id` VARCHAR( 36 ) NOT NULL ;");


        $entries = \App\Transaction::all();
        foreach ($entries as $entry) {
            $product = \App\Product::where(['wp_id' => $entry->product_id])->first();
            $testSuite = \App\LaravelTestSuite::where(['wp_id' => $entry->suite_minor_family_mark])->first();
            $testCase = \App\LaravelTestCase::where(['wp_id' => $entry->test_case_id])->first();
            if ($product && $testSuite && $testCase) {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `transactions` SET  `product_id`  = '{$product->id}' WHERE product_id = {$entry->product_id}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `transactions` SET  `suite_minor_family_mark`  = '{$testSuite->id}' WHERE suite_minor_family_mark = {$entry->suite_minor_family_mark}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `transactions` SET  `test_case_id`  = '{$testCase->id}' WHERE test_case_id = {$entry->test_case_id}");
            }
        }

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `verify_requests` CHANGE  `product_id`  `product_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `verify_requests` CHANGE  `test_suite_id`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");

        $entries = \App\VerifyRequest::all();
        foreach ($entries as $entry) {
            $product = \App\Product::where(['wp_id' => $entry->product_id])->first();
            $testSuite = \App\LaravelTestSuite::where(['wp_id' => $entry->suite_minor_family_mark])->first();
            if ($product && $testSuite) {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `verify_requests` SET  `product_id`  = '{$product->id}' WHERE product_id = {$entry->product_id}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `verify_requests` SET  `suite_minor_family_mark`  = '{$testSuite->id}' WHERE suite_minor_family_mark = {$entry->suite_minor_family_mark}");
            }
        }

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `test_plans` CHANGE  `product_id`  `product_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `test_plans` CHANGE  `suite_id`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");

        $entries = \App\TestPlan::all();
        foreach ($entries as $entry) {
            $product = \App\Product::where(['wp_id' => $entry->product_id])->first();
            $testSuite = \App\LaravelTestSuite::where(['wp_id' => $entry->suite_minor_family_mark])->first();
            if ($product && $testSuite) {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `test_plans` SET  `product_id`  = '{$product->id}' WHERE product_id = {$entry->product_id}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `test_plans` SET  `suite_minor_family_mark`  = '{$testSuite->id}' WHERE suite_minor_family_mark = {$entry->suite_minor_family_mark}");
            }
        }

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `wp_users_subscriptions` CHANGE  `suite_id`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("UPDATE  `wp_users_subscriptions` as us set suite_minor_family_mark = (SELECT id FROM test_suites WHERE wp_id = us.suite_minor_family_mark)");


        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `wp_organisations_subscriptions` CHANGE  `suite_family_mark`  `suite_minor_family_mark` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("UPDATE  `wp_organisations_subscriptions` as os set suite_minor_family_mark = (SELECT id FROM test_suites WHERE wp_id = os.suite_minor_family_mark)");

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `communities_organisations_approved_test_suites` CHANGE  `test_suite_id`  `suite_major_family_mark` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `communities_organisations_approved_products` CHANGE  `product_id`  `product_id` VARCHAR( 36 ) NOT NULL ;");

        \Illuminate\Support\Facades\DB::statement("UPDATE  `communities_organisations_approved_test_suites` as us set suite_major_family_mark = (SELECT major_family_mark FROM test_suites WHERE wp_id = us.suite_major_family_mark)");
        \Illuminate\Support\Facades\DB::statement("UPDATE  `communities_organisations_approved_products` as us set product_id = (SELECT id FROM products WHERE wp_id = us.product_id)");

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `test_plans_excluded_cases` CHANGE  `test_case_id`  `test_case_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("UPDATE  `test_plans_excluded_cases` as tp set test_case_id = (SELECT id FROM test_cases WHERE wp_id = tp.test_case_id)");

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `testing_details` CHANGE  `product_id`  `product_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `testing_details` CHANGE  `test_suite_id`  `test_suite_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `testing_details` CHANGE  `test_case_id`  `test_case_id` VARCHAR( 36 ) NOT NULL ;");

        $entries = \App\TestingDetail::all();
        foreach ($entries as $entry) {
            $product = \App\Product::where(['wp_id' => $entry->product_id])->first();
            $testSuite = \App\LaravelTestSuite::where(['wp_id' => $entry->test_suite_id])->first();
            $testCase = \App\LaravelTestCase::where(['wp_id' => $entry->test_case_id])->first();
            if ($product && $testSuite && $testCase) {
                \Illuminate\Support\Facades\DB::statement("UPDATE  `testing_details` SET  `product_id`  = '{$product->id}' WHERE product_id = {$entry->product_id}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `testing_details` SET  `test_suite_id`  = '{$testSuite->id}' WHERE test_suite_id = {$entry->test_suite_id}");
                \Illuminate\Support\Facades\DB::statement("UPDATE  `testing_details` SET  `test_case_id`  = '{$testCase->id}' WHERE test_case_id = {$entry->test_case_id}");
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
         Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `claims` CHANGE `suite_minor_family_mark` `test_suite_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `transactions` CHANGE  `suite_minor_family_mark` `test_suite_id`   VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `verify_requests` CHANGE  `suite_minor_family_mark` `test_suite_id`   VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `test_plans` CHANGE    `suite_minor_family_mark` `suite_id` VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `wp_users_subscriptions` CHANGE `suite_minor_family_mark` `suite_id`   VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `wp_organisations_subscriptions` CHANGE  `suite_minor_family_mark` `suite_family_mark`   VARCHAR( 36 ) NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `communities_organisations_approved_test_suites` CHANGE  `suite_major_family_mark` `test_suite_id` VARCHAR( 36 ) NOT NULL ;");
        Schema::enableForeignKeyConstraints();
    }
}
