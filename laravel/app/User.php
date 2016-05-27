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
        foreach($this->suiteSubscriptions as $subscription){
            $organisationSubscription = OrganisationSubscription::where(['user_id' => $this->ID, 'suite_family_mark' => $subscription->suite_family_mark])->first();
            $testPlans = TestPlan::where(['organisation_subscription_id' => $organisationSubscription->id, 'suite_id' => $subscription->suite_family_mark])->get();
            $suite = Post::find($subscription->suite_family_mark);
            $response[$suite->post_title] = [
                 'testSuite' => $suite,
                 'testPlans' => [],
            ];
            foreach($testPlans as $testPlan){
                 $response[$suite->post_title]['testPlans'][] = [
                     'product' => Post::find($testPlan->product_id),
                     'testPlan' => $testPlan,
                     'testPlanData' => [
                         'excludedCases' => $testPlan->getExcludedCases(),
                         'successCases' => $testPlan->getSuccessCases($testPlan->product_id),
                         'failedCases' => $testPlan->getFailedCases($testPlan->product_id),
                         'optionalCases' => $testPlan->getOptionalCases(),
                     ],
                 ];
            }
        }
        return $response;
    }

    public function getProducts()
    {
        //admin will see all products
        if(is_super_admin()){
            return Post::where(['post_type' => 'product-service'])->get();
        }

        $organisation = @$this->organisation[0];

        if(!$organisation){
            return [];
        }
        //usual user will see only his organisation's products
        return DB::table('wp_posts')
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
}
