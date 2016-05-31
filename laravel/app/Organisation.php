<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'wp_organisations';

    /**
     * Organisation memebers relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function members()
    {
        return $this->belongsTo('App\User', 'wp_organisations_members');
    }

    /**
     * Organisation subscriptions relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('App\OrganisationSubscription');
    }

    /**
     * Get organisation test plans. Only non-claimed test plans will be returned if $excludeClaimed flas is true
     * @param bool $productStringId
     * @param bool $excludeClaimed
     * @return array
     */
    public function getTestPlans($productStringId = false, $excludeClaimed = false)
    {
        $result = [];
        foreach ($this->subscriptions as $organisationSubscription) {

            $where = [
                'organisation_subscription_id' => $organisationSubscription->id,
                'suite_id' => $organisationSubscription->suite_family_mark
            ];
            if ($productStringId) {
                $product = Post::where(['post_name' => $productStringId])->first();
                $where['product_id'] = $product->ID;
            }
            $testPlans = TestPlan::where($where)->get();
            $suite = Post::find($organisationSubscription->suite_family_mark);

            foreach ($testPlans as $testPlan) {
                /*
                 * Exclude claimed test plans if $excludeClaimed flag was set to true
                 */
                if ($excludeClaimed && $testPlan->is_claimed) {
                    continue;
                }
                $product = Post::find($testPlan->product_id);
                $result[] = [
                    'id' => $testPlan->id,
                    'test_suite_id' => $suite->post_name,
                    'test_suite_title' => $suite->post_title,
                    'product_id' => $product->post_name,
                    'product_title' => $product->post_title,
                    'conformance_level' => $testPlan->level,
                ];
            }
        }

        return $result;
    }
}
