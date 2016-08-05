<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCommunitiesTablesCollation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
        \Illuminate\Support\Facades\DB::statement("alter table communities convert to character set utf8 collate utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("alter table communities_members convert to character set utf8 collate utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("SET FOREIGN_KEY_CHECKS = 1;");
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
