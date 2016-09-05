<?php

namespace App;


class TestPlanObserver
{

    use \App\CloudSearchDomainTrait;

    /*
     * @var CloudSearchDomainClient
     */
    private $_client = null;

    public function __construct()
    {
        $this->_client = $this->getDocumentEndpointCloudSearchClient(\App\WpOptions::where(['option_name' => 'cloudsearch_domain_name'])->first()->option_value);
    }

    /**
     * Listen to the TestPlan created event.
     * @param TestPlan $testPlan
     */
    public function saved(TestPlan $testPlan)
    {
        $product = Post::find($testPlan->product_id);

        $productVisibility = $product->getMetaByKey('product_visibility');

        $testSuite = Post::find($testPlan->suite_id);
        $testSuiteCommunity = $testSuite->getMetaByKey('community_id');

        if ($productVisibility == 'Public') {
            $visibility = 1;
            $communities = [1];
        } else {
            if ($productVisibility == 'Community') {
                $visibility = 2;
                $communities = [$testSuiteCommunity];
            } else {
                $visibility = 3;
                $communities = [$testSuiteCommunity];
            }
        }

        $productVersion = $product->getMetaByKey('product_version');
        $productOwner = Organisation::find($product->getMetaByKey('product_organisation_id'))->organisation_name;
        $productDescription = $product->getMetaByKey('product_description');
        $temp_data = array(
            'name' => $product->post_title,
            'version' => $productVersion,
            'owner' => $productOwner,
            'type' => 'Product',
            'test_suite' => $testSuite->post_title,
            'role' => [$testPlan->role],
            'level' => [$testPlan->level],
            'status' => 'In Progress',
            'test_type' => 'Certification',
            'date' => date('Y-m-d\TH:i:s') . 'Z',
            'for_search' => $productDescription . ' + ' . $productOwner . ' + ' . $testSuite->post_title . ' + Product + Certification + ' . $testPlan->role . ' + ' . $testPlan->level,
            'suite_id' => $testPlan->suite_id,
            'post_id' => $product->ID,
            'visibility' => $visibility,
            'community_id' => $communities,
            'user_id' => $testPlan->creator_id,
            'product_id' => $testPlan->product_id,
            'product_name' => $product->post_title,
            'start_date' => date('Y-m-d\TH:i:s', strtotime($testPlan->created_at)) . 'Z',
        );
        $this->_client->uploadDocuments([
            'documents' => json_encode([['type' => 'add', 'id' => 'test_plan_' . $testPlan->id, 'fields' => $temp_data]]),
            'contentType' => 'application/json'
        ]);
    }

    /**
     * Listen to the TestPlan deleting event.
     * @param TestPlan $testPlan
     */
    public function deleting(TestPlan $testPlan)
    {
        $this->_client->uploadDocuments([
            'documents' => json_encode([['type' => 'delete', 'id' => 'test_plan_' . $testPlan->id]]),
            'contentType' => 'application/json'
        ]);
    }

}
