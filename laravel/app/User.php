<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

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
        return $this->hasMany('App\CommunityMembers', 'user_id');
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
        $files = glob(__DIR__  . "/../../wp-content/uploads/avatars/".$this->ID."/*-".$type.".*");
        if(count($files) > 0){
            return getSiteUrl() . explode('/../..', $files[0])[1];
        }
        return DEFAULT_AVATAR;
    }

    /**
     * Subscribed test suites
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suiteSubscriptions()
    {
        return $this->hasMany('\App\OrganisationSubscription', 'user_id');
    }

    public function getUserTestPlans()
    {
        $response = [];
        $organisationSubscriptions = OrganisationSubscription::where(['organisation_id' => $this->organisation[0]->id])->get();

        foreach($organisationSubscriptions as $organisationSubscription){
            //user shouldn't see test plans for a test suite if he is not subscribed to test suite
            if (!OrganisationSubscription::where(['user_id' => $this->ID, 'suite_family_mark' => $organisationSubscription->suite_family_mark])->first()) {
                continue;
            }
            $testPlans = TestPlan::where(['is_claimed' => false, 'organisation_subscription_id' => $organisationSubscription->id, 'suite_id' => $organisationSubscription->suite_family_mark])->get();
            $suite = Post::find($organisationSubscription->suite_family_mark);
            if(!isset($response[$suite->post_title] )) {
                $response[$suite->post_title] = [
                    'testSuite' => $suite,
                    'testPlans' => [],
                ];
            }
            foreach($testPlans as $testPlan){

                foreach($testPlan->getSkippedTransactions($testPlan->product_id) as $skippedCase){
                    $testPlan->excludedCases()->updateOrCreate(
                        [
                            'test_case_id' => $skippedCase,
                        ],
                        [
                            'reason' => 'Test execution was skipped.',
                            'excluded_by_user_id' => $this->ID,
                            'is_skipped' => true
                        ]
                    );
                }
                 $response[$suite->post_title]['testPlans'][] = [
                     'product' => Post::find($testPlan->product_id),
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
        return $response;
    }

    public function getProducts($productType = 'DataSource')
    {
        $productType = str_replace(' ', '', $productType);
        $results = [];
        //admin will see all products
        if (is_super_admin()) {
            $products = Post::where(['post_type' => 'product-service'])->get();
        } else {

            $organisation = @$this->organisation[0];

            if (!$organisation) {
                return [];
            }
            //usual user will see only his organisation's products
            $products = DB::table('wp_posts')
                ->select('wp_posts.*', 'pm2.meta_value AS version', 'pm1.*')
                ->join('wp_postmeta AS pm1', function ($join) use ($organisation) {
                    $join->on('pm1.post_id', '=', 'wp_posts.ID')
                        ->where('pm1.meta_value', '=', $organisation->id)
                        ->where('pm1.meta_key', '=', 'product_organisation_id');
                })
                ->join('wp_postmeta AS pm2', function ($join) use ($organisation) {
                    $join->on('pm2.post_id', '=', 'wp_posts.ID')
                        ->where('pm2.meta_key', '=', 'product_version');
                })
                ->where('wp_posts.post_type', '=', 'product-service')
                ->groupBy('wp_posts.ID')->get();
        }
        foreach ($products as $product) {
            if (str_replace(' ', '', PostMeta::where(['post_id' => $product->ID, 'meta_key' => 'product_type'])->first()->meta_value) == $productType) {
                $results[] = $product;
            }
        }

        return $results;
    }
}
