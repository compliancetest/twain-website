<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProfileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `wp_community_profile_types` CHANGE `community_id` `community_id` VARCHAR( 36 ) NULL DEFAULT NULL ;');
        \Illuminate\Support\Facades\DB::statement('UPDATE wp_community_profile_types SET community_id = (SELECT id FROM communities ORDER BY created_at DESC LIMIT 1)');
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
