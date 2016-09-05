<?php

namespace App;


class PostObserver
{
    /**
     * Listen to the Post saved event.
     * @param Post $post
     */
    public function saved(Post $post)
    {
        if($post->post_type == 'product-service'){
            error_log('Post observer');
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
        }
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
