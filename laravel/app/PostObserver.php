<?php

namespace App;

class PostObserver
{
    use CloudSearchDomainTrait;

    /**
     * Listen to the Post saved event.
     * @param Post $post
     */
    public function saved(Post $post)
    {
        if($post->post_type == 'product-service'){
            $productTestPlans = TestPlan::where(['product_id' => $post->ID])->get();
            foreach($productTestPlans as $productTestPlan){
                $productTestPlan->timestamps = false;
                $productTestPlan->save();
            }
            $productClaims = Claim::where(['product_id' => $post->ID])->get();
            foreach($productClaims as $productClaim){
                $productClaim->timestamps = false;
                $productClaim->save();
            }

            //save product data to Fulltext search domain
            $cloudSearchClient = $this->getFulltextEndpointCloudSearchClient();

            $productDescription = $post->getMetaByKey('product_description');
            $productVisibility = $post->getMetaByKey('product_visibility');

            $groups = getUserCommunities($post->post_author);
            $communityNames = array();
            if ($groups) {
                foreach ($groups AS $group) {
                    $communityNames[] = $group->title;
                    $groups['groups'][] = $group->id;
                }
            }
            if (empty($communityNames)) {
                $communityNames = ['TWAIN'];
                $groups['groups'] = [Community::findBySlug('twain')->id];
            }

            $productData = array(
                'community' => $communityNames,
                'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($post->post_date)) . 'Z',
                'post_author_name' => cp_get_user_fullname($post->post_author),
                'post_author_id' => $post->post_author,
                'post_content' => (string) $productDescription,
                'post_status' => $post->post_status,
                'post_title' => $post->post_title,
                'post_type' => 'Product',
                'post_id' => $post->ID,
                'visibility' => $productVisibility == 'Public' ? 1 : $productVisibility == 'Community' ? 2 : 3,
                'community_id' => $groups['groups'],
                'for_search' => $productDescription . ' Product ' . $post->post_title .' ' . implode(' ', $communityNames) .' ' . $productVisibility,
                'link' => get_permalink($post->ID)
            );
            $cloudSearchClient->uploadDocuments([
                'documents' => json_encode([['type' => 'add', 'id' => $post->ID, 'fields' => $productData]]),
                'contentType' => 'application/json'
            ]);
        }
    }
}
