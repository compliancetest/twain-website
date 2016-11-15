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
        $this->_client = $this->getRegistryEndpointCloudSearchClient();
    }

    /**
     * Listen to the TestPlan created event.
     * @param TestPlan $testPlan
     */
    public function saved(TestPlan $testPlan)
    {
        $product = Product::find($testPlan->product_id);
        $testSuite = LaravelTestSuite::find($testPlan->suite_minor_family_mark);
        if (!$product || !$testSuite) {
            return;
        }
        $productVisibility = $product->visibility;

        $testSuiteCommunity = $testSuite->community_id;

        $communities = [$testSuiteCommunity];

        if ($productVisibility == 'Public') {
            $visibility = 1;
        } else {
            if ($productVisibility == 'Community') {
                $visibility = 2;
            } else {
                $visibility = 3;
            }
        }

        $productVersion = $product->version;
        $productOrganisationId = $product->organisation_id;
        $productOwner = Organisation::find($productOrganisationId)->organisation_name;
        $productDescription = $product->description;
        $temp_data = array(
            'name' => $product->full_name,
            'version' => $productVersion,
            'owner' => $productOwner,
            'type' => 'Product',
            'test_suite' => $testSuite->full_name,
            'role' => [$testPlan->role],
            'level' => [$testPlan->level],
            'status' => 'In Progress',
            'test_type' => 'Certification',
            'date' => date('Y-m-d\TH:i:s') . 'Z',
            'for_search' => $productDescription . ' + ' . $productOwner . ' + ' . $testSuite->full_name . ' + Product + Certification + ' . $testPlan->role . ' + ' . $testPlan->level,
            'suite_id' => $testPlan->suite_minor_family_mark,
            'post_id' => $product->id,
            'visibility' => $visibility,
            'community_id' => $communities,
            'user_id' => $testPlan->creator_id,
            'organisation_id' => $productOrganisationId,
            'product_id' => $testPlan->product_id,
            'product_name' => $product->full_name,
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
