<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    protected $table = 'wp_posts';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'post_title',
        'post_name',
        'post_type',
        'post_status',
        'post_author',
        'post_date',
        'comment_status',
        'ping_status'
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postmeta()
    {
        return $this->hasMany('App\PostMeta');
    }

    /**
     * Get meta key value
     * @param $metaKey
     * @return mixed
     */
    public function getMetaByKey($metaKey)
    {
        return @$this->postmeta()->where('meta_key', $metaKey)->first()->meta_value;
    }

    
    public static function getCommunityTestSuites($communityId)
    {
        return DB::table('wp_posts')
            ->join('wp_postmeta', function($join) use ($communityId){
                $join->on('wp_postmeta.post_id', '=', 'wp_posts.ID')
                     ->where('meta_key', '=', 'community_id')
                    ->where('meta_value', '=', $communityId);
            })
            ->get();
    }

    public function meta()
    {
        return $this->hasMany('App\PostMeta');
    }

    public static function getUniquePostName(\Illuminate\Database\Eloquent\Model $model, $value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count($model->whereRaw("post_name REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }

    /**
     * Get test suites test cases
     * @param array $roles
     * @param array $levels
     * @return mixed
     */
    public function getTestCases($roles = false, $levels = false)
    {
        $suiteId = $this->ID;
        $query = Post::select('wp_posts.*')
             ->join('wp_postmeta AS pm1', function ($join) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm1.meta_value', '=', 'Active')
                    ->where('pm1.meta_key', '=', 'test_case_status');
            })
            ->join('wp_postmeta AS pm2', function ($join) {
                $join->on('pm2.post_id', '=', 'wp_posts.ID')
                    ->where('pm2.meta_value', '=', '0')
                    ->where('pm2.meta_key', '=', 'hide_case');
            })
            ->join('wp_postmeta AS pm3', function ($join) use ($suiteId) {
                $join->on('pm3.post_id', '=', 'wp_posts.ID')
                    ->where('pm3.meta_value', '!=', 'Default')
                    ->where('pm3.meta_key', '=', 'conformance_level_'. $suiteId);
            })
            ->join('wp_postmeta AS pm4', function ($join) {
                    $join->on('pm4.post_id', '=', 'wp_posts.ID')
                        ->where('pm4.meta_key', 'LIKE', 'scenario_%');
                })
            ->join('wp_test_suites_scenarios AS scenario', function ($join) {
                $join->on('scenario.id', '=', 'pm4.meta_value');
            });


        if ($levels) {
            $query->join('wp_postmeta AS pm5', function ($join) use ($suiteId, $levels) {
                $join->on('pm5.post_id', '=', 'wp_posts.ID')
                    ->where('pm5.meta_value', '=', $levels)
                    ->where('pm5.meta_key', '=', 'conformance_level_' . $suiteId);
            });
        }
        if ($roles) {
            $query->join('wp_postmeta AS pm6', function ($join) use ($roles) {
                $join->on('pm6.post_id', '=', 'wp_posts.ID')
                    ->where('pm6.meta_value', '=', $roles)
                    ->where('pm6.meta_key', '=', 'choose_tester_role');
            });
        }
        $query->where('wp_posts.post_type', '=', 'test-case')
            ->groupBy('wp_posts.ID')
            ->orderBy('scenario.sequence')
            ->orderBy('wp_posts.post_title');
        $query->get();
        return $query->get();
    }

    /**
     * Get products with PENDING transactions for test suite
     * @return array
     */
    public function getProductsForNewVerifyRequest()
    {
        $response = [];
        $testSuiteEntry = TestSuite::where(['suite_id' => $this->ID])->first();
        $userSubscriptions = OrganisationSubscription::where(['user_id' => Auth::user()->ID, 'suite_family_mark' => $testSuiteEntry->family_mark])->get();
        foreach ($userSubscriptions as $userSubscription) {
            $productsWithPendingTransactions = Transaction::where([
                'subscription_id' => $userSubscription->id,
                'test_suite_id' => $testSuiteEntry->family_mark,
                'test_outcome_status_id' => TestOutcomeStatus::getIdByCode('PENDING')
            ])->groupBy('product_id')->get();
            if ($productsWithPendingTransactions) {
                foreach ($productsWithPendingTransactions as $productWithPendingTransactions) {
                    $product = Post::find($productWithPendingTransactions->product_id);
                    $response[$product->post_title] = $product;
                }
            }
        }
        ksort($response);
        return $response;
    }
}
