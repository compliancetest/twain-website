<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use UuidTrait, SlugTrait;

    public $incrementing = false;

    protected $fillable = [
        'slug', 'name', 'full_name', 'description', 'visibility', 'type', 'version',
        'manufacturer', 'protocol_version', 'model', 'access_url', 'organisation_id', 'user_id', 'wp_id',
        'created_at', 'updated_at'
    ];

    public $dates = ['released_at', 'created_at', 'updated_at'];

    public function capabilities()
    {
        return $this->hasMany('App\ProductCapability');
    }

    public function features()
    {
        return $this->hasMany('App\ProductFeature');
    }

    public function getFeatures()
    {
        $result = [];
        foreach($this->features as $feature){
            $testSuiteFeature = TestSuiteFeatures::find($feature->test_suites_feature_id);
            @$result[$testSuiteFeature->test_suite_id][] = [
                'name' => $testSuiteFeature->name,
                'description' => $testSuiteFeature->description,
            ];
        }
        return $result;
    }

    public function claims()
    {
        return $this->hasMany('App\Claim');
    }

    public function testPlans()
    {
        return $this->hasMany('App\TestPlan');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function verifyRequests()
    {
        return $this->hasMany('App\VerifyRequest');
    }

    /**
     * Check that user can edit / delete permission
     * @param User $user
     * @return bool
     */
    public function doesUserCanEdit(User $user)
    {
        return $user->check() && ($this->organisation_id == @$user->organisation[0]->id || is_super_admin());
    }

    public function getUrl()
    {
        return getSiteUrl() . '/product/' . $this->slug;
    }
}
