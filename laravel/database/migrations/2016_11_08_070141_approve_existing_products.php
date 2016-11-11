<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApproveExistingProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\CommunityOrganisationsApprovedProducts::truncate();
        $organisations = \App\Organisation::all();
        foreach (\App\Community::all() as $community) {
            foreach ($organisations as $organisation) {
                $organisationProducts = $organisation->getProducts();
                foreach ($organisationProducts as $product) {
                    \App\CommunityOrganisationsApprovedProducts::create([
                        'organisation_id' => $organisation->id,
                        'community_id' => $community->id,
                        'product_id' => !empty($product->ID) ? $product->ID : $product->id,
                        'approved_by' => 1
                    ]);
                }
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
