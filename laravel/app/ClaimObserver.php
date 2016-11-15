<?php

namespace App;


class ClaimObserver
{

   use CloudSearchDomainTrait;

    /*
     * @var CloudSearchDomainClient
     */
    private $_client = null;

    public function __construct()
    {
        $this->_client = $this->getRegistryEndpointCloudSearchClient();
    }

    /**
     * Listen to the Claim created event.
     * @param Claim $claim
     */
    public function saved(Claim $claim)
    {
        $product = Product::find($claim->product_id);
        $testSuite = LaravelTestSuite::find($claim->suite_minor_family_mark);
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
            'name' => (string) $product->full_name,
            'version' => $productVersion,
            'owner' => $productOwner,
            'type' => 'Product',
            'test_suite' => $testSuite->full_name,
            'role' => [$claim->role],
            'level' => [$claim->conformance_level],
            'status' => 'Verified',
            'test_type' => 'Certification',
            'date' => date('Y-m-d\TH:i:s') . 'Z',
            'for_search' => $productDescription . ' + ' . $productOwner . ' + ' . $testSuite->full_name . ' + Product + Certification + ' . $claim->role . ' + ' . $claim->conformance_level,
            'suite_id' => $claim->suite_minor_family_mark,
            'post_id' => $product->id,
            'visibility' => $visibility,
            'community_id' => $communities,
            'user_id' => $claim->creator_id,
            'organisation_id' => $productOrganisationId,
            'product_id' => $claim->product_id,
            'product_name' => $product->full_name,
            'start_date' => date('Y-m-d\TH:i:s', strtotime($claim->created_at)) . 'Z',
            'cert_number' => $claim->id,
            'cert_url' => $claim->getPdfUrl()
        );
        $this->_client->uploadDocuments([
            'documents' => json_encode([['type' => 'add', 'id' => 'claim_' . $claim->id, 'fields' => $temp_data]]),
            'contentType' => 'application/json'
        ]);
    }

    /**
     * Listen to the Claim deleting event.
     * @param TestPlan $testPlan
     */
    public function deleting(Claim $claim)
    {
        $this->_client->uploadDocuments([
            'documents' => json_encode([['type' => 'delete', 'id' => 'claim_' . $claim->id]]),
            'contentType' => 'application/json'
        ]);
    }

}
