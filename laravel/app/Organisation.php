<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Organisation extends Model
{

    protected $guarded = ['id'];

    protected $table = 'wp_organisations';

    public $timestamps = false;

    /**
     * Get products_organisations mutator
     * @param $value
     * @return array|mixed
     */
    public function getProductsOrganisationsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Set products_organisations mutator
     * @param $value
     */
    public function setProductsOrganisationsAttribute($value)
    {
        $this->attributes['products_organisations'] = json_encode(array_map('trim', explode(',', $value)));
    }

    /**
     * Organisation memebers relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function members()
    {
        return $this->belongsTo('App\User', 'wp_organisations_members');
    }

    /**
     * Organisation members list
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membersList()
    {
        return $this->hasMany('App\OrganisationMember')->with(['user']);
    }

    /**
     * Organisation subscriptions relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('App\OrganisationSubscription')->with(['testSuite']);
    }

    public function approvedSuites()
    {
        return $this->hasMany(\App\CommunityOrganisationsApprovedTestSuites::class);
    }

    /**
     * Organisation products
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products()
    {
        return $this->hasMany('App\Product', 'organisation_id');
    }

    /**
     * get organisation products sorted by type and name
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products()
            ->orderBy('type')
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Get organisation test plans. Only non-claimed test plans will be returned if $excludeClaimed flas is true
     * @param bool $productSlug
     * @param bool $excludeClaimed
     * @return array
     */
    public function getTestPlans($productSlug = false, $excludeClaimed = true)
    {
        $result = [];
        foreach ($this->subscriptions as $organisationSubscription) {

            $where = [
                'organisation_subscription_id' => $organisationSubscription->id,
                'suite_minor_family_mark' => $organisationSubscription->suite_minor_family_mark
            ];

            if ($productSlug) {
                $product = Product::findBySlug($productSlug);
                $where['product_id'] = $product->id;
            }

            $testPlans = TestPlan::where($where)->get();
            $suite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($organisationSubscription->suite_minor_family_mark);

            foreach ($testPlans as $testPlan) {
                /*
                 * Exclude claimed test plans if $excludeClaimed flag was set to true
                 */
                if ($excludeClaimed && $testPlan->is_claimed) {
                    continue;
                }

                // we shouldn't show test plans to user without subscription
                if (!\Auth::user()->suiteSubscriptions()->where(['suite_minor_family_mark' => $testPlan->suite_minor_family_mark])->first()) {
                    continue;
                }

                $product = Product::find($testPlan->product_id);
                $result[] = [
                    'id' => $testPlan->id,
                    'test_suite_id' => $suite->slug,
                    'test_suite_title' => $suite->full_name,
                    'product_id' => $product->slug,
                    'product_title' => $product->full_name,
                    'conformance_level' => $testPlan->level,
                ];
            }
        }

        return $result;
    }

    public function testSuiteIsApproved(LaravelTestSuite $testSuite)
    {
        return $this->approvedSuites()->where('suite_major_family_mark', $testSuite->major_family_mark)->first();
    }
}
