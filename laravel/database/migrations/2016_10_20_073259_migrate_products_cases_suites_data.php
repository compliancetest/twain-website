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

        $products = \App\Post::where(['post_type' => 'product-service'])->get();
        foreach ($products as $product) {
            \App\Product::create([
                'slug' => $product->post_name,
                'name' => $product->post_title,
                'full_name' => $product->getProductFullName(),
                'description' => $product->getMetaByKey('product_description'),
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
                'created_at' => $product->post_date,
                'updated_at' => $product->post_date,
            ]);
        }


        $testSuites = \App\Post::where(['post_type' => 'test-suite'])->get();
        foreach ($testSuites as $testSuite) {
            $testSuiteFullName = $testSuite->post_title . ' v' . $testSuite->getMetaByKey('ts_version_major') . '.' . $testSuite->getMetaByKey('ts_version_minor');
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
                'name' => $testSuite->post_title,
                'version_major' => $testSuite->getMetaByKey('ts_version_major'),
                'version_minor' => $testSuite->getMetaByKey('ts_version_minor'),
                'version_patch' => $testSuite->getMetaByKey('ts_version_patch'),
                'description' => $testSuite->getMetaByKey('ts_description'),
                'full_name' => $testSuiteFullName,
                'short_name' => $testSuite->getMetaByKey('ts_identifier'),
                'issuer' => $testSuite->getMetaByKey('ts_issuer'),
                'revision_description' => $testSuite->getMetaByKey('ts_revision_description'),
                'status' => $testSuite->getMetaByKey('ts_status'),
                'product_type' => $testSuite->getMetaByKey('ts_tester_role'),
                'excerpt' => $testSuite->post_excerpt,
                'major_family_mark' => \App\TestSuite::getTestSuiteFamilyMark($testSuite->ID),
                'wp_id' => $testSuite->ID,
                'published_at' => $testSuite->getMetaByKey('ts_issue_date'),
                'created_at' => $testSuite->post_date,
                'updated_at' => $testSuite->post_date,
            ]);

            $laravelTestSuite->minor_family_mark = $testSuite->id;
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
                        $laravelTestSuite->conformanceLevels()->create(['code' => $code, 'description' => (string)$descs[$i]]);
                    }
                }
            }

            //features
            $features = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'featuresList'])->first();
            if ($features) {
                $features = json_decode($features->meta_value, true);
                foreach ($features as $feature) {
                    if (!empty($feature)) {
                        $laravelTestSuite->features()->create(['name' => $feature['name'], 'description' => $feature['description']]);
                    }
                }
            }

            //scenarios

            $scenarios = DB::select("SELECT * FROM wp_test_suites_scenarios WHERE suite_id = ? ORDER BY sequence", [$testSuite->ID]);
            foreach ($scenarios as $scenario) {
                $laravelTestSuite->scenarios()->create(['code' => $scenario->code, 'description' => $scenario->description, 'sequence' => $scenario->sequence]);
            }

            //profile types
            $profileTypes = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'ts_profile_types'])->first();
            if ($profileTypes) {
                $profileTypes = explode(';;', $profileTypes->meta_value);
                foreach ($profileTypes as $profileType) {
                    if(!empty($profileType)) {
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
                if($ids) {
                    foreach ($ids as $i => $id) {
                        if (!empty($id)) {
                            $ls = \App\LaravelTestSuite::where('wp_id', $id)->first();
                            if ($ls) {
                                $laravelTestSuite->relatedTestSuites()->create(['related_test_suite_id' => $ls->id, 'description' => (string)$descs[$i]]);
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
                if($roles) {
                    foreach ($roles as $i => $role) {
                        if (!empty($role)) {
                            $laravelTestSuite->roles()->create(['name' => $role, 'description' => (string)$descs[$i]]);
                        }
                    }
                }
            }

            //specification documents
            $specificationDocuments = DB::select("SELECT * FROM wp_ts_options_documents WHERE ts_id = ? ORDER BY id", [$testSuite->ID]);
            foreach ($specificationDocuments as $specificationDocument) {
                if (!empty($specificationDocument->doc_loc_url)) {
                    $laravelTestSuite->specificationDocuments()->create(['name' => $specificationDocument->doc_name, 'description' => $specificationDocument->doc_desc, 'link' => $specificationDocument->doc_loc_url]);
                }
            }

            //protocol_versions
            $protocolVersions = \App\PostMeta::where(['post_id' => $testSuite->ID, 'meta_key' => 'protocol_versions'])->first();
            if ($protocolVersions) {
                $protocolVersions = json_decode($protocolVersions->meta_value);
                if($protocolVersions) {
                    foreach ($protocolVersions as $i => $protocolVersion) {
                        if (!empty($protocolVersion)) {
                            $laravelTestSuite->protocolVersions()->create(['version' => $protocolVersion]);
                        }
                    }
                }
            }


            //todo - pricing plans
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
        Schema::enableForeignKeyConstraints();
    }
}

