<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTablesCollation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("SET foreign_key_checks = 0;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `communities_downloads` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `communities_members` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `communities_meta` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `communities_profiles_backups` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `communities_testsuites` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `community_articles` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `community_forum_posts` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `community_forum_threads` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `community_forum_threads_read` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `community_invitations` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `community_surveys_results` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `flash_messages` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `communities` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `claim_transactions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `claims` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `api_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `testing_details` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `test_outcome_statuses` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `test_plans` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `test_plans_excluded_cases` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transaction_change_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `verify_requests` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `communities_members` CHANGE  `community_id`  `community_id` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");
        \Illuminate\Support\Facades\DB::statement("SET foreign_key_checks = 1;");
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
