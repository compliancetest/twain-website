<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixDataSourceTestPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("UPDATE `test_plans` SET role = 'DataSource' WHERE role = 'Data Source'");
        \Illuminate\Support\Facades\DB::statement("UPDATE `wp_postmeta` SET meta_value = 'DataSource' WHERE meta_key = 'ts_tester_role' AND meta_value = 'Data Source'");
        \Illuminate\Support\Facades\DB::statement("UPDATE `wp_postmeta` SET meta_value = 'DataSource' WHERE meta_key = 'choose_tester_role' AND meta_value = 'Data Source'");
        \Illuminate\Support\Facades\DB::statement("UPDATE wp_postmeta
SET meta_value = REPLACE(meta_value, 'Data Source', 'DataSource')
WHERE meta_value LIKE '%Data Source%' and meta_key = 'role_names'");
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
