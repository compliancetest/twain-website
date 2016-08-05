<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTicketTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE  `wp_tickets` CHANGE  `community_id`  `community_id` VARCHAR( 36 ) NULL DEFAULT NULL ;");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_categories` (`id`, `category_title`, `category_name`, `has_fee`, `sort_number`, `tickets`, `created_date`) VALUES (NULL, 'Billing Enquiry', 'billing-enquiry', '0', '1', '0', NULL);");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_categories` (`id`, `category_title`, `category_name`, `has_fee`, `sort_number`, `tickets`, `created_date`) VALUES (NULL, 'Bug Report', 'bug-report', '0', '2', '0', NULL);");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_categories` (`id`, `category_title`, `category_name`, `has_fee`, `sort_number`, `tickets`, `created_date`) VALUES (NULL, 'Support Request', 'support-request', '1', '3', '0', NULL);");

        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_priorities` (`id`, `priority`, `item_code`, `ttresponse`, `ttresolve`, `sort_number`) VALUES (NULL, 'Normal', 'SUPPORT-N', '24.00', '72.00', '1');");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_priorities` (`id`, `priority`, `item_code`, `ttresponse`, `ttresolve`, `sort_number`) VALUES (NULL, 'High', 'SUPPORT-H', '12.00', '48.00', '2');");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_priorities` (`id`, `priority`, `item_code`, `ttresponse`, `ttresolve`, `sort_number`) VALUES (NULL, 'Urgent', 'SUPPORT-U', '6.00', '24.00', '3');");

        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_statuses` (`id`, `status`, `sort_number`) VALUES (NULL, 'New', '1');");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_statuses` (`id`, `status`, `sort_number`) VALUES (NULL, 'In Progress', '2');");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_statuses` (`id`, `status`, `sort_number`) VALUES (NULL, 'Resolved', '3');");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_statuses` (`id`, `status`, `sort_number`) VALUES (NULL, 'Closed', '4');");
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `twain`.`wp_ticket_statuses` (`id`, `status`, `sort_number`) VALUES (NULL, 'Feedback', '5');");
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
