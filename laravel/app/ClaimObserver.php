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
        $this->_client = $this->getDocumentEndpointCloudSearchClient(\App\WpOptions::where(['option_name' => 'cloudsearch_domain_name'])->first()->option_value);
    }

    /**
     * Listen to the Claim created event.
     * @param Claim $claim
     */
    public function saved(Claim $claim)
    {
        $product = Post::find($claim->product_id);
        $testSuite = Post::find($claim->test_suite_id);
        if (!$product || !$testSuite) {
            return;
        }

        $productVisibility = $product->getMetaByKey('product_visibility');


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
            'role' => [$claim->role],
            'level' => [$claim->conformance_level],
            'status' => 'Verified',
            'test_type' => 'Certification',
            'date' => date('Y-m-d\TH:i:s') . 'Z',
            'for_search' => $productDescription . ' + ' . $productOwner . ' + ' . $testSuite->post_title . ' + Product + Certification + ' . $claim->role . ' + ' . $claim->conformance_level,
            'suite_id' => $claim->test_suite_id,
            'post_id' => $product->ID,
            'visibility' => $visibility,
            'community_id' => $communities,
            'user_id' => $claim->creator_id,
            'product_id' => $claim->product_id,
            'product_name' => $product->post_title,
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
