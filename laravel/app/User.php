<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{

    protected $table = 'wp_users';

    protected $primaryKey = 'ID';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_email', 'user_pass', 'user_login',
    ];

    protected $username = 'user_email';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_pass',
    ];

    public function getAuthPassword()
    {
        return $this->user_pass;
    }

    /**
     * Organisation membersip
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organisation()
    {
        return $this->belongsToMany('App\Organisation', 'wp_organisations_members');
    }

    public function threads()
    {
        return $this->hasMany('\App\ForumThread', 'ID');
    }

    /**
     * Relation with wp_usermeta table
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('\App\UserMeta', 'user_id');
    }

    public function forumPosts()
    {
        return $this->hasMany('\App\ForumThreadPost', 'ID');
    }

    public function getFullName()
    {
        return cp_get_user_fullname($this->ID);
    }

    /**
     * Community subscriptions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('App\CommunityMembers', 'user_id')->with('community');
    }

    /**
     * User confirmed community memberships
     * @return mixed
     */
    public function confirmedSubscriptions()
    {
        return $this->subscriptions()->where(['is_confirmed' => 1])->get();
    }

    /**
     * Get User profile image
     * @param string $type
     * @return string
     */
    public function getAvatar($type = 'bpthumb')
    {
        $key = get_user_meta($this->ID, 'avatar_s3_path', true);
        if (Storage::exists($key)) {
            return 'https://s3-us-west-2.amazonaws.com/'.config('env.bucket.website').'/' . $key;
        }
        return DEFAULT_AVATAR;
    }

    /**
     * Subscribed test suites
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suiteSubscriptions()
    {
        return $this->hasMany('\App\OrganisationSubscription', 'user_id')->with('testSuite');
    }

    public function getUserTestPlans()
    {
        $response = [];
        $organisationSubscriptions = OrganisationSubscription::where(['organisation_id' => $this->organisation[0]->id])->get();

        foreach($organisationSubscriptions as $organisationSubscription){

            $aprovementEntry = CommunityOrganisationsApprovedTestSuites::where(['organisation_id' => $this->organisation[0]->id, 'suite_major_family_mark' => LaravelTestSuite::getMajorFamilyMarkForMinorFamilyMark($organisationSubscription->suite_minor_family_mark)])->first();
            //user shouldn't see test plans for a test suite if he is not subscribed to test suite or if organisation doesn't have approvement for this suite
            if (!OrganisationSubscription::where(['user_id' => $this->ID, 'suite_minor_family_mark' => $organisationSubscription->suite_minor_family_mark])->first() || !$aprovementEntry) {
                continue;
            }
            $testPlans = TestPlan::where(['is_claimed' => false, 'organisation_subscription_id' => $organisationSubscription->id, 'suite_minor_family_mark' => $organisationSubscription->suite_minor_family_mark])->get();
            $suite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($organisationSubscription->suite_minor_family_mark);
            if(!isset($response[$suite->full_name] )) {
                $response[$suite->full_name] = [
                    'testSuite' => $suite,
                    'testPlans' => [],
                ];
            }
            foreach($testPlans as $testPlan){
                 $response[$suite->full_name]['testPlans'][] = [
                     'product' => Product::find($testPlan->product_id),
                     'testPlan' => $testPlan,
                     'testPlanData' => [
                         'excludedCases' => $testPlan->getExcludedCases(),
                         'successCases' => $testPlan->getSuccessCases($testPlan->product_id),
                         'failedCases' => $testPlan->getFailedCases($testPlan->product_id),
                         'optionalCases' => $testPlan->getOptionalCases(),
                         'skippedCases' => $testPlan->getSkippedCases(),
                     ],
                 ];
            }
        }

        //bubble sorting for test plans
        foreach ($response as $testSuite => $testSuiteData) {
            $testPlans = $testSuiteData['testPlans'];
            $size = count($testPlans) - 1;
            for ($i = $size; $i >= 0; $i--) {
                for ($j = 0; $j <= ($i - 1); $j++)
                    if (strtolower($testPlans[$j]['product']->full_name . $testPlans[$j]['testPlan']->level) > strtolower($testPlans[$j + 1]['product']->full_name . $testPlans[$j + 1]['testPlan']->level)) {
                        $k = $testPlans[$j];
                        $testPlans[$j] = $testPlans[$j + 1];
                        $testPlans[$j + 1] = $k;
                    }
            }
            $response[$testSuite]['testPlans'] = $testPlans;
        }
        return $response;
    }

    public function getProducts($productType = 'DataSource', $protocolVersions = false)
    {
        //admin will see all products
        if (is_super_admin()) {
            $productsQuery = Product::orderBy('full_name');
        } else {

            $organisation = @$this->organisation[0];

            if (!$organisation) {
                return [];
            }
            //usual user will see only his organisation's products
            $productsQuery = Product::where('organisation_id', $organisation->id)->orderBy('full_name');
        }

        if($productType){
            $productsQuery->where('type', $productType);
        }
         if($protocolVersions){
            $productsQuery->whereIn('protocol_version', $protocolVersions);
        }
        return $productsQuery->get();
    }

    public function getMetaByKey($metaKey)
    {
        $meta = $this->meta()->where('meta_key', $metaKey)->first();
        if ($meta) {
            return $meta->meta_value;
        }
        return false;
    }

    /**
     * Get user's transactions per page number
     * @return bool|int
     */
    public function getTransactionsPerPage()
    {
        $count = $this->getMetaByKey('transactions_per_page');
        if($count){
            return $count;
        }
        $this->meta()->create([
            'meta_key' => 'transactions_per_page',
            'meta_value' => 25,
        ]);
        return 25;
    }

    /**
     * Get suite subscription
     * @param LaravelTestSuite $testSuite
     * @return mixed
     */
    public function getSuiteSubscription(LaravelTestSuite $testSuite)
    {
        return $this->suiteSubscriptions()->where('suite_minor_family_mark', $testSuite->minor_family_mark)->first();
    }

    /**
     * Ensure that user's organisation has access to a given test suite
     * @param LaravelTestSuite $testSuite
     * @return bool
     */
    public function doesUserOrganisationApproved(LaravelTestSuite $testSuite)
    {
        $organisation = $this->organisation;
        if(!empty($organisation[0])){
            return $organisation[0]->testSuiteIsApproved($testSuite);
        }
        return false;
    }
}
