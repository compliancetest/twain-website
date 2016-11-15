<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    use CloudSearchDomainTrait;

    public function saved(Product $product)
    {
        $productTestPlans = TestPlan::where(['product_id' => $product->id])->get();
        foreach ($productTestPlans as $productTestPlan) {
            $productTestPlan->timestamps = false;
            $productTestPlan->save();
        }
        $productClaims = Claim::where(['product_id' => $product->id])->get();
        foreach ($productClaims as $productClaim) {
            $productClaim->timestamps = false;
            $productClaim->save();
        }

        //save product data to Fulltext search domain
        $cloudSearchClient = $this->getFulltextEndpointCloudSearchClient();

        $productDescription = $product->description;
        $productVisibility = $product->visibility;

        $groups = User::find($product->user_id)->subscriptions;
        $communityNames = array();
        if (count($groups)) {
            foreach ($groups AS $group) {
                $communityNames[] = $group->community->title;
                $groupsData[] = $group->community->id;
            }
        }
        if (empty($communityNames)) {
            $communityNames = ['TWAIN'];
            $groupsData = [Community::findBySlug('twain')->id];
        }

        $productData = array(
            'community' => $communityNames,
            'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($product->updated_at)) . 'Z',
            'post_author_name' =>  User::find($product->user_id)->getFullName(),
            'post_author_id' => $product->user_id,
            'post_content' => (string)$productDescription,
            'post_status' => 'Published',
            'post_title' => $product->full_name,
            'post_type' => 'Product',
            'post_id' => $product->id,
            'visibility' => $productVisibility == 'Public' ? 1 : $productVisibility == 'Community' ? 2 : 3,
            'community_id' => $groupsData,
            'for_search' => $productDescription . ' Product ' . $product->full_name . ' ' . implode(' ', $communityNames) . ' ' . $productVisibility,
            'link' => $product->getUrl()
        );

        $cloudSearchClient->uploadDocuments([
            'documents' => json_encode([['type' => 'add', 'id' => $product->id, 'fields' => $productData]]),
            'contentType' => 'application/json'
        ]);
    }
}
