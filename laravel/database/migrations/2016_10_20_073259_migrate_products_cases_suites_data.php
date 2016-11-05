<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateProductsCasesSuitesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->cleanData();

        $testSuites = \App\Post::where(['post_type' => 'test-suite'])->get();
        foreach ($testSuites as $testSuite) {
            $testSuiteFullName = $testSuite->getMetaByKey('ts_name') . ' v' .$testSuite->getMetaByKey('ts_version_major').'.'.$testSuite->getMetaByKey('ts_version_minor');
            $patch = $testSuite->getMetaByKey('ts_version_patch');
            if ($patch) {
                $testSuiteFullName .= '.' . $patch;
            }
            if (!\App\Community::find($testSuite->getMetaByKey('community_id'))) {
                continue;
            }
            $laravelTestSuite = \App\LaravelTestSuite::create([
                'community_id' => $testSuite->getMetaByKey('community_id'),
                'slug' => $testSuite->post_name,
                'name' => $testSuite->getMetaByKey('ts_name'),
                'version_major' => $testSuite->getMetaByKey('ts_version_major'),
                'version_minor' => $testSuite->getMetaByKey('ts_version_minor'),
                'version_patch' => $testSuite->getMetaByKey('ts_version_patch'),
                'description' => strip_tags($testSuite->getMetaByKey('ts_description')),
                'full_name' => $testSuiteFullName,
                'short_name' => $testSuite->getMetaByKey('ts_identifier'),
                'issuer' => $testSuite->getMetaByKey('ts_issuer'),
                'revision_description' => strip_tags($testSuite->getMetaByKey('ts_revision_description')),
                'status' => $testSuite->getMetaByKey('ts_status'),
                'product_type' => $testSuite->getMetaByKey('ts_tester_role'),
                'excerpt' => strip_tags($testSuite->post_excerpt),
                'wp_id' => $testSuite->ID,
                'published_at' => $testSuite->getMetaByKey('ts_issue_date'),
                'created_at' => $testSuite->post_date,
                'updated_at' => $testSuite->post_date,
            ]);

            $laravelTestSuite->major_family_mark = $laravelTestSuite->id;
            $laravelTestSuite->minor_family_mark = $laravelTestSuite->id;
            $laravelTestSuite->save();

            //test suite types
            $laravelTestSuite->types()->create(['type' => 'Data Exchange']);

            //conformance levels
            $codes = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'lvl_code'])->first();
            $descs = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'lvl_desc'])->first();
            if ($codes) {
                $codes = unserialize($codes->meta_value);
                $descs = unserialize($descs->meta_value);
                foreach ($codes as $i => $code) {
                    if (!empty($code)) {
                        $laravelTestSuite->conformanceLevels()->create(['code' => $code, 'description' => strip_tags((string)$descs[$i])]);
                    }
                }
            }

            //features
            $features = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'featuresList'])->first();
            if ($features) {
                $features = json_decode($features->meta_value, true);
                foreach ($features as $feature) {
                    if (!empty($feature)) {
                        $laravelTestSuite->features()->create(['name' => $feature['name'], 'description' => strip_tags($feature['description'])]);
                    }
                }
            }

            //scenarios

            $scenarios = DB::select("SELECT * FROM wp_test_suites_scenarios WHERE suite_id = ? ORDER BY sequence", [$testSuite->ID]);
            foreach ($scenarios as $scenario) {
                $laravelTestSuite->scenarios()->create(['code' => $scenario->code, 'description' => strip_tags($scenario->description), 'sequence' => $scenario->sequence]);
            }

            //profile types
            $profileTypes = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'ts_profile_types'])->first();
            if ($profileTypes) {
                $profileTypes = explode(';;', $profileTypes->meta_value);
                foreach ($profileTypes as $profileType) {
                    if (!empty($profileType)) {
                        $laravelTestSuite->profileTypes()->create(['profile_type_id' => $profileType]);
                    }
                }
            }

            //related test suites

            $ids = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'ts'])->first();
            $descs = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'ts_desc'])->first();
            if ($ids) {
                $ids = unserialize($ids->meta_value);
                $descs = unserialize($descs->meta_value);
                if ($ids) {
                    foreach ($ids as $i => $id) {
                        if (!empty($id)) {
                            $ls = \App\LaravelTestSuite::where('wp_id', $id)->first();
                            if ($ls) {
                                $laravelTestSuite->relatedTestSuites()->create(['related_test_suite_id' => $ls->id, 'description' => strip_tags((string)$descs[$i])]);
                            }
                        }
                    }
                }
            }

            //roles

            $roles = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'role_names'])->first();
            $descs = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'role_descs'])->first();
            if ($roles) {
                $roles = explode('|', $roles->meta_value);
                $descs = explode('|', $descs->meta_value);
                if ($roles) {
                    foreach ($roles as $i => $role) {
                        if (!empty($role)) {
                            $laravelTestSuite->roles()->create(['name' => $role, 'description' => strip_tags((string)$descs[$i])]);
                        }
                    }
                }
            }

            //specification documents
            $specificationDocuments = DB::select("SELECT * FROM wp_ts_options_documents WHERE ts_id = ? ORDER BY id", [$testSuite->ID]);
            foreach ($specificationDocuments as $specificationDocument) {
                if (!empty($specificationDocument->doc_loc_url)) {
                    $laravelTestSuite->specificationDocuments()->create(['name' => $specificationDocument->doc_name, 'description' => strip_tags($specificationDocument->doc_desc), 'link' => $specificationDocument->doc_loc_url]);
                }
            }

            //protocol_versions
            $protocolVersions = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'protocol_versions'])->first();
            if ($protocolVersions) {
                $protocolVersions = json_decode($protocolVersions->meta_value);
                if ($protocolVersions) {
                    foreach ($protocolVersions as $i => $protocolVersion) {
                        if (!empty($protocolVersion)) {
                            $laravelTestSuite->protocolVersions()->create(['version' => $protocolVersion]);
                        }
                    }
                }
            }
        }

        $testCases = \App\Post::where(['post_type' => 'test-case'])->get();
        foreach ($testCases as $testCase) {
            $testCaseFullName = $testCase->post_title;
            $patch = $testCase->getMetaByKey('version_patch');
            if ($patch) {
                $testCaseFullName .= '.' . $patch;
            }
            $laravelTestCase = \App\LaravelTestCase::create([
                'slug' => $testCase->post_name,
                'name' => $testCase->post_title,
                'version_major' => (integer)$testCase->getMetaByKey('version_major'),
                'version_minor' => (integer)$testCase->getMetaByKey('version_minor'),
                'version_patch' => (integer)$testCase->getMetaByKey('version_patch'),
                'description' => strip_tags((string)$testCase->getMetaByKey('test_intent_description')),
                'full_name' => $testCaseFullName,
                'tester_role' => $testCase->getMetaByKey('choose_tester_role'),
                'harness_role' => $testCase->getMetaByKey('choose_harness_role'),
                'initiator' => $testCase->getMetaByKey('choose_initiator'),
                'status' => (string) $testCase->getMetaByKey('test_case_status'),
                'test_execution_profile_id' => (integer)$testCase->getMetaByKey('test_execution'),
                'configuration_profile_id' => (integer)$testCase->getMetaByKey('test_data_profile'),
                'outcome_type' => (string)$testCase->getMetaByKey('outcome_type'),
                'is_optional' => $testCase->getMetaByKey('testcase_status') == 'Yes' ? true : false,
                'test_pattern' => strip_tags((string)$testCase->getMetaByKey('message_count')),
                'wp_id' => $testCase->ID,
                'published_at' => $testCase->getMetaByKey('published'),
                'created_at' => $testCase->post_date,
                'updated_at' => $testCase->post_date,
            ]);

            //conformance levels
            $testCaseSuite = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'test_suite'])->get();
            if ($testCaseSuite) {
                foreach ($testCaseSuite as $caseSuite) {
                    $conformanceLevels = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'conformance_level_' . $caseSuite->meta_value])->get();
                    if ($conformanceLevels) {
                        foreach ($conformanceLevels as $conformanceLevel) {
                            $laravelSuite = \App\LaravelTestSuite::where('wp_id', $caseSuite->meta_value)->first();
                            if ($laravelSuite) {
                                $suiteLevel = $laravelSuite->conformanceLevels()->where('code', $conformanceLevel->meta_value)->first();
                                if ($suiteLevel) {
                                    $laravelTestCase->conformanceLevels()->create([
                                        'test_suite_id' => $laravelSuite->id,
                                        'conformance_level_id' => $suiteLevel->id
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            //test cases scenarios
            $testCaseSuite = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'test_suite'])->get();
            if ($testCaseSuite) {
                foreach ($testCaseSuite as $caseSuite) {
                    $scenarios = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'scenario_' . $caseSuite->meta_value])->get();
                    if ($scenarios) {
                        foreach ($scenarios as $scenario) {
                            $laravelSuite = \App\LaravelTestSuite::where('wp_id', $caseSuite->meta_value)->first();
                            if ($laravelSuite) {
                                $wpScenario = DB::select("SELECT * FROM wp_test_suites_scenarios WHERE suite_id = ? AND id = ? ORDER BY sequence", [$caseSuite->meta_value, $scenario->meta_value]);
                                if (isset($wpScenario[0]->code)) {
                                    $suiteScenario = $laravelSuite->scenarios()->where('code', $wpScenario[0]->code)->first();
                                    if ($suiteScenario) {
                                        $laravelTestCase->scenarios()->create([
                                            'test_suites_scenario_id' => $suiteScenario->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //test case roles
            $testCaseSuite = \App\PostMeta::where(['post_id' => $laravelTestCase->wp_id, 'meta_key' => 'test_suite'])->get();
            if ($testCaseSuite) {
                foreach ($testCaseSuite as $caseSuite) {
                    $roles = \App\PostMeta::where(['post_id' => $caseSuite->post_id, 'meta_key' => 'choose_tester_role'])->first();
                    if ($roles) {
                        $roles = explode('|', $roles->meta_value);
                        if ($roles) {
                            foreach ($roles as $i => $role) {
                                if (!empty($role)) {
                                    $laravelSuite = \App\LaravelTestSuite::where('wp_id', $caseSuite->meta_value)->first();
                                    if ($laravelSuite) {
                                        $suiteRole = \App\TestSuiteRole::where(['name' => $role, 'test_suite_id' => $laravelSuite->id])->first();
                                        if ($suiteRole) {
                                            $laravelTestCase->roles()->create(['test_suites_role_id' => $suiteRole->id]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //test_cases_samples
            $imagesData = \App\PostMeta::where(['post_id' => $laravelTestCase->wp_id, 'meta_key' => 'imagesData'])->first();
            if ($imagesData) {
                $imagesData = json_decode($imagesData->meta_value, true);
                if ($imagesData) {
                    foreach ($imagesData as $imageData) {
                        if (!empty($imageData['name'])) {
                            $laravelTestCase->samples()->create([
                                'image' => $imageData['name'],
                                'description' => strip_tags((string)$imageData['description'])
                            ]);
                        }
                    }
                }
            }

            $featuresData = \App\PostMeta::where(['post_id' => $laravelTestCase->wp_id, 'meta_key' => 'featuresList'])->first();
            if ($featuresData) {
                $featuresData = json_decode($featuresData->meta_value, true);
                if ($featuresData) {
                    foreach ($featuresData as $feature) {
                        $testCaseSuite = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'test_suite'])->get();
                        if ($testCaseSuite) {
                            foreach ($testCaseSuite as $caseSuite) {
                                $laravelSuite = \App\LaravelTestSuite::where('wp_id', $caseSuite->meta_value)->first();
                                if ($laravelSuite) {
                                    $laravelFeature = $laravelSuite->features()->where('name', $feature)->first();
                                    if ($laravelFeature) {
                                        $laravelTestCase->features()->create([
                                            'test_suites_feature_id' => $laravelFeature->id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //capabilities
            $capabilitiesData = \App\PostMeta::where(['post_id' => $laravelTestCase->wp_id, 'meta_key' => 'capabilities'])->first();
            if ($capabilitiesData) {
                $capabilitiesData = json_decode($capabilitiesData->meta_value, true);
                if ($capabilitiesData) {
                    foreach ($capabilitiesData as $capability) {
                        $laravelTestCase->capabilities()->create([
                            'capability' => strip_tags($capability)
                        ]);
                    }
                }
            }

            //test steps
            $stepsActions = \App\PostMeta::where(['post_id' => $laravelTestCase->wp_id, 'meta_key' => 'step_action'])->first();
            $stepsResults = \App\PostMeta::where(['post_id' => $laravelTestCase->wp_id, 'meta_key' => 'step_expected'])->first();
            if ($stepsActions) {
                $stepsActions = unserialize($stepsActions->meta_value);
                $stepsResults = unserialize($stepsResults->meta_value);
                if ($stepsActions) {
                    foreach ($stepsActions as $i => $stepsAction) {
                        if (!empty($stepsAction)) {
                            $laravelTestCase->steps()->create([
                                'action' => strip_tags($stepsAction),
                                'expected_result' => strip_tags($stepsResults[$i]),
                                'step' => $i + 1
                            ]);
                        }
                    }
                }
            }

            //test_suite_test_case

            $testCaseSuite = \App\PostMeta::where(['post_id' => $testCase->ID, 'meta_key' => 'test_suite'])->get();
            if ($testCaseSuite) {
                foreach ($testCaseSuite as $caseSuite) {
                    if (!empty($caseSuite->meta_value)) {
                        $lSuite = \App\LaravelTestSuite::where('wp_id', $caseSuite->meta_value)->first();
                        if ($lSuite) {
                            $laravelTestCase->testSuites()->save($lSuite);
                        }
                    }
                }
            }

        }

        $products = \App\Post::where(['post_type' => 'product-service'])->get();
        foreach ($products as $product) {
            $laravelProduct = \App\Product::create([
                'slug' => $product->post_name,
                'name' => $product->post_title,
                'full_name' => $product->getProductFullName(),
                'description' => strip_tags($product->getMetaByKey('product_description')),
                'visibility' => $product->getMetaByKey('product_visibility'),
                'type' => $product->getMetaByKey('product_type'),
                'version' => $product->getMetaByKey('product_version'),
                'manufacturer' => (string)$product->getMetaByKey('product_manufacturer'),
                'protocol_version' => $product->getMetaByKey('protocol_version'),
                'model' => (string)$product->getMetaByKey('model'),
                'access_url' => (string)$product->getMetaByKey('product_url'),
                'organisation_id' => $product->getMetaByKey('product_organisation_id'),
                'user_id' => $product->post_author,
                'wp_id' => $product->ID,
                'released_at' => $product->post_date,
                'created_at' => $product->post_date,
                'updated_at' => $product->post_date,
            ]);

            $capabilitiesData = \App\PostMeta::where(['post_id' => $laravelProduct->wp_id, 'meta_key' => 'capabilities'])->first();
            if ($capabilitiesData) {
                $capabilitiesData = json_decode($capabilitiesData->meta_value, true);
                if ($capabilitiesData) {
                    foreach ($capabilitiesData as $capability) {
                        $laravelProduct->capabilities()->create([
                            'capability' => $capability
                        ]);
                    }
                }
            }
            $featuresData = \App\PostMeta::where(['post_id' => $laravelProduct->wp_id, 'meta_key' => 'product_features'])->first();
            if ($featuresData) {
                $featuresData = json_decode($featuresData->meta_value, true);
                if ($featuresData) {
                    foreach ($featuresData as $feature) {
                        $productSuites = \App\PostMeta::where(['post_id' => $laravelProduct->wp_id, 'meta_key' => 'product_suites'])->first();
                        if ($productSuites) {
                            $productSuites = json_decode($productSuites->meta_value, true);
                            if ($productSuites) {
                                foreach ($productSuites as $productSuite) {
                                    $laravelSuite = \App\LaravelTestSuite::where('wp_id', $productSuite)->first();
                                    if ($laravelSuite) {
                                        $laravelFeature = $laravelSuite->features()->where('name', $feature)->first();
                                        if ($laravelFeature) {
                                            $laravelProduct->features()->create([
                                                'test_suites_feature_id' => $laravelFeature->id
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $testSuites = \App\Post::where(['post_type' => 'test-suite'])->get();
        foreach ($testSuites as $testSuite) {
            $laravelTestSuite = \App\LaravelTestSuite::where('wp_id', $testSuite->ID)->first();
            $ll = \App\LaravelTestSuite::where('wp_id', \App\TestSuite::find($testSuite->ID)->family_mark)->first();
            if ($ll) {
                $laravelTestSuite->major_family_mark = $ll->id;
                $laravelTestSuite->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->cleanData();
    }

    public function cleanData()
    {
        Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::statement("truncate products_features");
        \Illuminate\Support\Facades\DB::statement("truncate products_capabilities");
        \Illuminate\Support\Facades\DB::statement("truncate products");

        \Illuminate\Support\Facades\DB::statement("truncate test_suites");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_conformance_levels");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_profile_types");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_related_suites");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_specification_documents");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_protocols_versions");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_scenarios");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_features");
        \Illuminate\Support\Facades\DB::statement("truncate test_suites_types");

        \Illuminate\Support\Facades\DB::statement("truncate test_cases_conformance_levels");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_scenarios");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_roles");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_samples");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_features");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_features");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_test_steps");
        \Illuminate\Support\Facades\DB::statement("truncate test_cases_capabilities");

        \Illuminate\Support\Facades\DB::statement("truncate test_suite_test_case");


        \Illuminate\Support\Facades\DB::statement("truncate test_cases");
        Schema::enableForeignKeyConstraints();
    }
}

