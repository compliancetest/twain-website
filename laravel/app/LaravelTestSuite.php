<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaravelTestSuite extends Model
{
    use UuidTrait, SlugTrait;

    protected $table = 'test_suites';

    public $incrementing = false;

    protected $fillable = [
        'community_id', 'slug', 'name', 'full_name', 'description', 'version_major', 'version_minor', 'version_patch',
        'short_name', 'issuer', 'revision_description', 'status', 'product_type', 'excerpt', 'minor_family_mark',
        'major_family_mark', 'wp_id', 'published_at', 'minor_family_mark', 'updated_at', 'created_at'
    ];

    public function getUrl()
    {
        return getSiteUrl() . '/test-suite/' . $this->slug;
    }
    public function testCases()
    {
        return $this->belongsToMany('App\LaravelTestCase', 'test_suite_test_case', 'test_suite_id', 'test_case_id');
    }

    public function changesSubscriptions()
    {
        return $this->hasMany('App\TestSuiteChangesSubscriptions', 'test_suite_id')->with('user');
    }

    /**
     * Get filtered / ordered by scenario sequence test cases
     * @param array $args
     * @return mixed
     */
    public function getOrderedCases($args = [], $isAdmin = false)
    {
        return $this->getCases($args, $isAdmin)->get();
    }

    public function types()
    {
        return $this->hasMany('\App\TestSuiteTypes', 'test_suite_id');
    }

    public function conformanceLevels()
    {
        return $this->hasMany('\App\TestSuiteConformanceLevels', 'test_suite_id')->where('code', '!=', 'Default')->orderBy('code');
    }

    public function features()
    {
        return $this->hasMany('\App\TestSuiteFeatures', 'test_suite_id');
    }

    public function scenarios()
    {
        return $this->hasMany('\App\TestSuiteScenarios', 'test_suite_id')->orderBy('sequence');
    }

    public function profileTypes()
    {
        return $this->hasMany('\App\TestSuiteProfileType', 'test_suite_id');
    }

    public function getProfileTypes()
    {
        $profiletypes = [];
        foreach ($this->profileTypes as $profileType) {
            $profileType = ProfileType::find($profileType->profile_type_id);
            $profiletypes[str_replace('.', '', $profileType->getVersion())] = ['title' => $profileType->getTitle(), 'id' => $profileType->id ];
        }
        ksort($profiletypes);
        return $profiletypes;
    }

    public function relatedTestSuites()
    {
        return $this->hasMany('\App\TestSuiteRelatedSuite', 'test_suite_id');
    }

    public function roles()
    {
        return $this->hasMany('\App\TestSuiteRole', 'test_suite_id');
    }

    public function specificationDocuments()
    {
        return $this->hasMany('\App\TestSuiteSpecificationDocument', 'test_suite_id');
    }

    public function protocolVersions()
    {
        return $this->hasMany('\App\TestSuiteProtocolVersion', 'test_suite_id');
    }

    public function claims()
    {
        return $this->hasMany('\App\Claim', 'suite_minor_family_mark');
    }

    public function transactions()
    {
        return $this->hasMany('\App\Transaction', 'suite_minor_family_mark');
    }

    public function testPlans()
    {
        return $this->hasMany('\App\TestPlan', 'suite_minor_family_mark');
    }

    /**
     * Subscriptions to test suite
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribers()
    {
        return $this->hasMany('\App\OrganisationSubscription', 'suite_minor_family_mark');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function community()
    {
        return $this->belongsTo('App\Community', 'community_id');
    }

    /**
     * Get latest suite id for given suite_minor_family_mark
     * @param $suiteMinorFamilyMark
     * @return mixed
     */
    public static function getLatestSuiteForMinorFamilyMark($suiteMinorFamilyMark)
    {
        return self::where(['minor_family_mark' => $suiteMinorFamilyMark])
            ->orderBy('created_at', 'DESC')->first();
    }

    /**
     * Get latest suite id for given suite_minor_family_mark
     * @param $suiteMinorFamilyMark
     * @return mixed
     */
    public static function getMajorFamilyMarkForMinorFamilyMark($suiteMinorFamilyMark)
    {
        return self::where(['minor_family_mark' => $suiteMinorFamilyMark])
            ->orderBy('created_at', 'DESC')->first()->major_family_mark;
    }

    /**
     * get test suite's test cases list
     * @return mixed
     */
    public function getTestCases($role = false, $level = false)
    {
        $query = $this->testCases()
            ->join('test_cases_scenarios as cs', function ($join) {
                $join->on('test_cases.id', '=', 'cs.test_case_id');
            })
            ->join('test_suites_scenarios as ss', function ($join) {
                $join->on('ss.id', '=', 'cs.test_suites_scenario_id');
            })
            ->join('test_cases_conformance_levels as sl', function ($join) {
                $join->on('test_cases.id', '=', 'sl.test_case_id');
            })
            ->join('test_suites_conformance_levels as tsl', function ($join) {
                $join->on('sl.conformance_level_id', '=', 'tsl.id');
            })
            ->where('status', 'Active')
            ->groupBy('test_cases.id')
            ->orderBy('scenarioCode')
            ->orderBy('ss.sequence')
            ->orderBy('test_cases.full_name')
            ->select('test_cases.*', 'ss.code AS scenarioCode', 'ss.description AS scenarioDescription', 'ss.id AS scenarioID');

        if ($level) {
            $query->where('tsl.code', '=', $level);
        }
        if ($role) {
            $query->where('tester_role', '=', $role);
        }
        return $query->get();
    }

    /**
     * Get cases filtered by provided fields
     * @param array $args
     * @param bool $isAdmin
     * @return mixed
     */
    public function getCases($args = [], $isAdmin = false)
    {
        $query = $this->testCases()
            ->select('test_cases.*', 'test_suites_scenarios.code AS scenarioCode', 'test_suites_scenarios.description AS scenarioDescription', 'test_suites_scenarios.id AS scenarioId')
            ->join('test_cases_conformance_levels', 'test_cases.id', '=', 'test_cases_conformance_levels.test_case_id')
            ->join('test_suites_conformance_levels', 'test_suites_conformance_levels.id', '=', 'test_cases_conformance_levels.conformance_level_id')
            ->leftjoin('test_cases_scenarios', 'test_cases.id', '=', 'test_cases_scenarios.test_case_id')
            ->leftjoin('test_suites_scenarios', 'test_suites_scenarios.id', '=', 'test_cases_scenarios.test_suites_scenario_id');
        if($args['orderBy']) {
            $query->orderBy($args['orderBy'], isset($args['order']) ? $args['orderBy'] : 'asc');
        }
        if(!empty($args['groupBy'])) {
            $query->groupBy($args['groupBy']);
        } else {
            $query->groupBy('test_cases.id');
        }
        if(!empty($args['role'])){
            $query->where('test_cases.tester_role', $args['role']);
        }
        if(!empty($args['level'])){
            $query->where('test_suites_conformance_levels.code', $args['level']);
        }
        if(!empty($args['execution_mode'])){
            $query->where('execution_mode', $args['execution_mode']);
        }
        if(!$isAdmin){
            $query->where('test_cases.status', 'Active');
        }
        if (!empty($args['scenario'])) {
            $query->where('test_suites_scenarios.code', $args['scenario']);
        }
        if (!empty($args['status'])) {
            $query->where('test_cases.status', $args['status']);
        }
        if (!empty($args['conformance_level'])) {
            $query->where('test_suites_conformance_levels.code', $args['conformance_level']);
        }
        $query
            ->orderBy('test_suites_scenarios.sequence')
            ->orderBy('scenarioCode')
            ->orderBy('test_cases.full_name');
        return $query;
    }

    /**
     * Process request data and update all test suite relations
     * @param $request
     */
    public function updateRelations($request)
    {
        $conformanceLevelCodes = [];
        if (is_array($request->get('conformanceLevels'))) {
            foreach ($request->get('conformanceLevels') as $subName => $conformanceLevel) {
                if ($subName == 'code') {
                    foreach ($conformanceLevel as $key => $code) {
                        $conformanceLevelCodes[] = $code;
                        if (isset($request->get('conformanceLevels')['id'][$key])) {
                            $entry = $this->conformanceLevels()->find($request->get('conformanceLevels')['id'][$key]);
                            if ($entry) {
                                $entry->code = $code;
                                $entry->description = @$request->get('conformanceLevels')['description'][$key];
                                $entry->save();
                            }
                        } else {
                            $this->conformanceLevels()->updateOrCreate(['code' => $code], [
                                'description' => @$request->get('conformanceLevels')['description'][$key]
                            ]);
                        }
                    }
                    if (!empty($conformanceLevelCodes)) {
                        $this->conformanceLevels()->whereNotIn('code', $conformanceLevelCodes)->delete();
                    }
                }
            }
        }

        //roles
        $processedEntries = [];
        if (is_array($request->get('roles'))) {
            foreach ($request->get('roles') as $subName => $row) {
                if ($subName == 'name') {
                    foreach ($row as $key => $name) {
                        $processedEntries[] = $name;
                        if (isset($request->get('roles')['id'][$key])) {
                            $entry = $this->roles()->find($request->get('roles')['id'][$key]);
                            if ($entry) {
                                $entry->name = $name;
                                $entry->description = @$request->get('roles')['description'][$key];
                                $entry->save();
                            }
                        } else {
                            $this->roles()->updateOrCreate(['name' => $name], [
                                'description' => @$request->get('roles')['description'][$key]
                            ]);
                        }
                    }
                    if (!empty($processedEntries)) {
                        $this->roles()->whereNotIn('name', $processedEntries)->delete();
                    }
                }
            }
        }

        //save scenarios
        $processedEntries = [];
        if ($request->get('scenarios')) {
            foreach ($request->get('scenarios') as $subName => $row) {
                if ($subName == 'code') {
                    foreach ($row as $key => $name) {
                        $processedEntries[] = $name;
                        if (isset($request->get('scenarios')['id'][$key])) {
                            $entry = $this->scenarios()->find($request->get('scenarios')['id'][$key]);
                            if ($entry) {
                                $entry->code = $name;
                                $entry->description = @$request->get('scenarios')['description'][$key];
                                $entry->sequence = @$request->get('scenarios')['sequence'][$key];
                                $entry->save();
                            }
                        } else {
                            $this->scenarios()->updateOrCreate(['code' => $name], [
                                'description' => @$request->get('scenarios')['description'][$key],
                                'sequence' => @$request->get('scenarios')['sequence'][$key],
                            ]);
                        }
                    }
                    if (!empty($processedEntries)) {
                        $this->scenarios()->whereNotIn('code', $processedEntries)->delete();
                    }
                }
            }
        }

        //save profileTypes
        $processedEntries = [];
        if (is_array($request->get('profile_types'))) {
            foreach ($request->get('profile_types') as $profiletypeId => $row) {
                $processedEntries[] = $profiletypeId;
                $this->profileTypes()->updateOrCreate(['profile_type_id' => $profiletypeId]);
            }
            if (!empty($processedEntries)) {
                $this->profileTypes()->whereNotIn('profile_type_id', $processedEntries)->delete();
            }
        }

        if (is_array($request->get('test_suite_type'))) {
            $processedEntries = [];
            foreach ($request->get('test_suite_type') as $testSuiteType) {
                $processedEntries[] = $testSuiteType;
                $this->types()->updateOrCreate(['type' => $testSuiteType]);
            }
            if (!empty($processedEntries)) {
                $this->types()->whereNotIn('type', $processedEntries)->delete();
            }
        }

        //specification documents
        $processedEntries = [];
        if (is_array($request->get('specificationDocuments'))) {
            foreach ($request->get('specificationDocuments') as $subName => $row) {
                if ($subName == 'name') {
                    foreach ($row as $key => $name) {
                        $processedEntries[] = $name;
                        $link = @$request->get('specificationDocuments')['link'][$key];
                        $this->specificationDocuments()->updateOrCreate(['name' => $name], [
                            'description' => @$request->get('specificationDocuments')['description'][$key],
                            'link' => $link,
                        ]);
                    }
                    if (!empty($processedEntries)) {
                        $this->specificationDocuments()->whereNotIn('name', $processedEntries)->delete();
                    }
                }
            }
        }

        //features
        $processedEntries = [];
        if (is_array($request->get('features'))) {
            foreach ($request->get('features') as $subName => $row) {
                if ($subName == 'name') {
                    foreach ($row as $key => $name) {
                        $processedEntries[] = $name;
                        if (isset($request->get('features')['id'][$key])) {
                            $entry = $this->features()->find($request->get('features')['id'][$key]);
                            if ($entry) {
                                $entry->name = $name;
                                $entry->description = @$request->get('features')['description'][$key];
                                $entry->save();
                            }
                        } else {
                            $this->features()->updateOrCreate(['name' => $name], [
                                'description' => @$request->get('features')['description'][$key],
                            ]);
                        }
                    }
                    if (!empty($processedEntries)) {
                        $this->features()->whereNotIn('name', $processedEntries)->delete();
                    }
                }
            }
        }

        //protocol versions
        if (is_array(explode(',', @$request->get('protocol_versions')))) {
            $processedEntries = [];
            foreach (explode(',', @$request->get('protocol_versions')) as $row) {
                $row = trim($row);
                $processedEntries[] = $row;
                if (!empty($row)) {
                    $this->protocolVersions()->updateOrCreate(['version' => $row]);
                }
            }
            if (!empty($processedEntries)) {
                $this->protocolVersions()->whereNotIn('version', $processedEntries)->delete();
            }
        }

        //related suites
        $processedEntries = [];
        if (is_array($request->get('related_ts'))) {
            foreach ($request->get('related_ts') as $subName => $row) {
                if ($subName == 'suite_id') {
                    foreach ($row as $key => $name) {
                        $processedEntries[] = $name;
                        if (self::find($name)) {
                            $this->relatedTestSuites()->updateOrCreate(['related_test_suite_id' => $name]);
                        }
                    }
                    if (!empty($processedEntries)) {
                        $this->relatedTestSuites()->whereNotIn('related_test_suite_id', $processedEntries)->delete();
                    }
                }
            }
        }
    }

    /**
     *
     * @param $request
     * @return bool
     */
    public function isVersionUpdated($request)
    {
        if ($this->version_major < $request->get('version_major') ||
            $this->version_minor < $request->get('version_minor') ||
            $this->version_patch < $request->get('version_patch')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check next version existence
     * @param null $versionMinor
     * @param null $versionPatch
     * @return mixed
     */

    function isNextVersionExist($versionMinor = null, $versionPatch = null)
    {
        if ($versionMinor === null && $versionPatch === null) {
            return self::where([
                'minor_family_mark' => $this->name,
                'version_major' => ($this->version_major + 1)
            ])->first();
        } else if ($versionPatch === null) {
            return self::where([
                'name' => $this->name,
                'version_major' => ($this->version_major),
                'version_minor' => ($this->version_minor + 1)
            ])->first();
        } else {
            return self::where([
                'name' => $this->name,
                'version_major' => ($this->version_major),
                'version_minor' => $this->version_minor,
                'version_patch' => ($this->version_patch + 1)
            ])->first();
        }
    }

    /**
     * Send notification about change to subscribed users
     */
    public function notifySubscribers()
    {
        $community = $this->community;

        $emailData = array(
            '[community]' => $community->title,
            '[community_url]' => $community->getUrl(),
            '[suite_name]' => $this->full_name,
            '[suite_url]' => $this->getUrl(),
            '[editor_name]' => cp_get_user_fullname(Auth::user()->ID)
        );

        if (count($this->changesSubscriptions)) {
            foreach ($this->changesSubscriptions as $member) {
                if ($member->user_id != Auth::user()->ID) {
                    $emailData['[name]'] = cp_get_user_fullname($member->user_id);
                    cp_send_email(array('name' => $emailData['[name]'], 'email' => $member->user->user_email), 'suite_changed', $emailData);
                }
            }
        }
    }

     /**
     * Get products with PENDING transactions for test suite
     * @return array
     */
    public function getProductsForNewVerifyRequest()
    {
        $response = [];
        $userSubscriptions = OrganisationSubscription::where(['organisation_id' => Auth::user()->suiteSubscriptions[0]->organisation_id, 'suite_minor_family_mark' => $this->minor_family_mark])->get();
        foreach ($userSubscriptions as $userSubscription) {
            $productsWithPendingTransactions = Transaction::where([
                'subscription_id' => $userSubscription->id,
                'test_outcome_status_id' => TestOutcomeStatus::getIdByCode('PENDING'),
                'suite_minor_family_mark' => $this->minor_family_mark,
            ])->groupBy('product_id')->get();
            if (count($productsWithPendingTransactions)) {
                foreach ($productsWithPendingTransactions as $productWithPendingTransactions) {
                    $product = Product::find($productWithPendingTransactions->product_id);
                    $response[$product->full_name] = $product;
                }
            }
        }
        ksort($response);
        return $response;
    }
}
