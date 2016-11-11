<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateProductsSuitesCases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        $this->dropIfExistsData();
        Schema::create('test_suites', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->string('slug');
            $table->string('name');//TWAIN v2.3 Compliance - Data Sources
            $table->integer('version_major');//1
            $table->integer('version_minor');//2
            $table->integer('version_patch');//0
            $table->text('description');//...
            $table->string('full_name');//TWAIN v2.3 Compliance - Data Sources v1.2
            $table->string('short_name');//TWAINDS
            $table->string('issuer');//twain.org
            $table->text('revision_description');//First Draft
            $table->string('status');//Active
            $table->string('product_type');//DataSource
            $table->text('excerpt');//DataSource
            $table->uuid('minor_family_mark');//
            $table->uuid('major_family_mark');//
            $table->integer('wp_id');//..
            $table->date('published_at')->nullable();//2016-01-29
            $table->timestamps();
        });

        Schema::table('test_suites', function ($table) {
            $table->foreign('community_id')->references('id')->on('communities');
        });

        Schema::create('test_suites_changes_subscriptions', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::table('test_suites_changes_subscriptions', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_types', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('type');
            $table->timestamps();
        });

        Schema::table('test_suites_types', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_conformance_levels', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('code');//A
            $table->text('description');//Conformance level A represents full compliance with all mandatory components of the TWAIN specification.
            $table->timestamps();
        });

        Schema::table('test_suites_conformance_levels', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_features', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('name');//Feature1
            $table->text('description');//Feature1 desc
            $table->timestamps();
        });

        Schema::table('test_suites_features', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_scenarios', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('code');//SC
            $table->text('description');//Standard Capability. TWAIN Standard capabilities (ID's with a value less than 0x8000). Ignore Vendor Custom capabilities (ID’s with a value of 0x8000 or greater).
            $table->integer('sequence');
            $table->timestamps();
        });

        Schema::table('test_suites_scenarios', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_profile_types', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->integer('profile_type_id');
            $table->timestamps();
        });

        Schema::table('test_suites_profile_types', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_related_suites', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->uuid('related_test_suite_id');
            $table->text('description');
            $table->timestamps();
        });

        Schema::table('test_suites_related_suites', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
            $table->foreign('related_test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_roles', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        Schema::table('test_suites_roles', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_specification_documents', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('name');
            $table->text('description');
            $table->text('link');
            $table->timestamps();
        });

        Schema::table('test_suites_specification_documents', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_suites_protocols_versions', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_suite_id');
            $table->string('version');
            $table->timestamps();
        });

        Schema::table('test_suites_protocols_versions', function ($table) {
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('test_cases', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->string('slug');
            $table->string('name');//CN-10
            $table->string('execution_mode');//Auto
            $table->integer('version_major');//1
            $table->integer('version_minor');//2
            $table->integer('version_patch');//0
            $table->text('description');//...
            $table->string('full_name');//CN-10 v1.2
            $table->string('tester_role');//Application
            $table->string('harness_role');//DataSource
            $table->string('initiator');//Tester
            $table->string('status');//Active
            $table->integer('test_execution_profile_id');//12
            $table->integer('configuration_profile_id');//12
            $table->string('outcome_type');//Positive
            $table->boolean('is_optional');//true
            $table->text('test_pattern');//..
            $table->integer('wp_id');//..
            $table->date('published_at')->nullable();//2016-01-29
            $table->timestamps();
        });

         Schema::table('test_cases', function ($table) {
            $table->foreign('community_id')->references('id')->on('communities');
        });

        Schema::create('test_cases_conformance_levels', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->uuid('conformance_level_id');
            $table->timestamps();
        });

        Schema::table('test_cases_conformance_levels', function ($table) {
            $table->foreign('conformance_level_id')->references('id')->on('test_suites_conformance_levels')->onDelete('cascade');
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_cases_scenarios', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->uuid('test_suites_scenario_id');
            $table->timestamps();
        });

        Schema::table('test_cases_scenarios', function ($table) {
            $table->foreign('test_suites_scenario_id')->references('id')->on('test_suites_scenarios')->onDelete('cascade');
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_cases_roles', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->uuid('test_suites_role_id');
            $table->timestamps();
        });

        Schema::table('test_cases_roles', function ($table) {
            $table->foreign('test_suites_role_id')->references('id')->on('test_suites_roles')->onDelete('cascade');
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_cases_samples', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->text('image');
            $table->text('description');
            $table->timestamps();
        });

        Schema::table('test_cases_samples', function ($table) {
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_cases_features', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->uuid('test_suites_feature_id');
            $table->timestamps();
        });

        Schema::table('test_cases_features', function ($table) {
            $table->foreign('test_suites_feature_id')->references('id')->on('test_suites_features')->onDelete('cascade');
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_cases_test_steps', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->text('action');
            $table->text('expected_result');
            $table->integer('step');
            $table->timestamps();
        });

        Schema::table('test_cases_test_steps', function ($table) {
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_cases_capabilities', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('test_case_id');
            $table->string('capability');
            $table->timestamps();
        });

        Schema::table('test_cases_capabilities', function ($table) {
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

        Schema::create('test_suite_test_case', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('test_case_id');
            $table->uuid('test_suite_id');
        });

        Schema::table('test_suite_test_case', function ($table) {
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
            $table->foreign('test_suite_id')->references('id')->on('test_suites')->onDelete('cascade');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->string('slug');
            $table->string('name');
            $table->string('full_name');
            $table->text('description');
            $table->string('visibility');
            $table->string('type');
            $table->string('version');
            $table->string('manufacturer');
            $table->string('protocol_version');
            $table->string('model');
            $table->text('access_url');
            $table->integer('organisation_id');
            $table->integer('user_id');
            $table->integer('wp_id');
            $table->date('released_at')->nullable();
            $table->timestamps();
        });

        Schema::create('products_features', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('product_id');
            $table->uuid('test_suites_feature_id');
            $table->timestamps();
        });

        Schema::table('products_features', function ($table) {
            $table->foreign('test_suites_feature_id')->references('id')->on('test_suites_features')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('products_capabilities', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('product_id');
            $table->string('capability');
            $table->timestamps();
        });

        Schema::table('products_capabilities', function ($table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        $this->dropIfExistsData();
        Schema::enableForeignKeyConstraints();
    }

    public function dropIfExistsData()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('products_features');
        Schema::dropIfExists('products_capabilities');
        Schema::dropIfExists('products');

        Schema::dropIfExists('test_suites_types');
        Schema::dropIfExists('test_suites_conformance_levels');
        Schema::dropIfExists('test_suites_features');
        Schema::dropIfExists('test_suites_scenarios');
        Schema::dropIfExists('test_suites_profile_types');
        Schema::dropIfExists('test_suites_related_suites');
        Schema::dropIfExists('test_suites_roles');
        Schema::dropIfExists('test_suites_specification_documents');
        Schema::dropIfExists('test_suites_protocols_versions');

        Schema::dropIfExists('test_cases_conformance_levels');
        Schema::dropIfExists('test_cases_scenarios');
        Schema::dropIfExists('test_cases_roles');
        Schema::dropIfExists('test_cases_samples');
        Schema::dropIfExists('test_cases_features');
        Schema::dropIfExists('test_cases_test_steps');
        Schema::dropIfExists('test_cases_capabilities');

        Schema::dropIfExists('test_suite_test_case');
        Schema::dropIfExists('test_cases');
        Schema::dropIfExists('test_suites');
        Schema::dropIfExists('test_suites_changes_subscriptions');
        Schema::enableForeignKeyConstraints();
    }
}
