<?php
Route::group(array('prefix' => 'api', 'middleware' => 'api.logs'), function () {
    Route::get('apiversion', ['uses' => '\App\Api\v1\Controllers\VersionController@apiversion']);
});
Route::group(array('prefix' => 'api/v1', 'middleware' => 'api.logs'), function () {

    Route::post('echo', ['uses' => '\App\Api\v1\Controllers\EchoController@index']);

    Route::get('version', ['uses' => '\App\Api\v1\Controllers\VersionController@index', 'middleware' => ['simpleauth']]);

    Route::get('testcase', ['uses' => '\App\Api\v1\Controllers\TestCasesController@show', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\v1\Controllers\TestCasesController@profiles', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::post('testcase/start', ['uses' => '\App\Api\v1\Controllers\TestCasesController@start', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::delete('testcase/stop', ['uses' => '\App\Api\v1\Controllers\TestCasesController@stop', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('testcase/status', ['uses' => '\App\Api\v1\Controllers\TestCasesController@status', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::get('testsuites', ['uses' => '\App\Api\v1\Controllers\TestSuitesController@index', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('testsuites/{suiteId}/testcases', ['uses' => '\App\Api\v1\Controllers\TestSuitesController@testcases', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::get('profiles/{profile}', ['uses' => '\App\Api\v1\Controllers\ProfilesController@show', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::post('transactions', ['uses' => '\App\Api\v1\Controllers\TransactionsController@create', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::post('products', ['uses' => '\App\Api\v1\Controllers\ProductsController@create', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test', 'organisation.subscriptions.product_type']]);
    Route::get('products', ['uses' => '\App\Api\v1\Controllers\ProductsController@get', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('products/{productId}/features', ['uses' => '\App\Api\v1\Controllers\ProductsController@listFeatures', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test', 'post.product.exist']]);
    Route::post('products/{productId}/features', ['uses' => '\App\Api\v1\Controllers\ProductsController@saveFeatures', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test', 'post.product.exist']]);

    //test plans
    Route::get('testplans', ['uses' => '\App\Api\v1\Controllers\TestPlansController@index', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('testplans/{testPlanId}/testcases', ['uses' => '\App\Api\v1\Controllers\TestPlansController@testcases', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

});

Route::group(array('prefix' => 'api/v2', 'middleware' => 'api.logs'), function () {

    Route::post('echo', ['uses' => '\App\Api\v2\Controllers\EchoController@index']);

    Route::get('version', ['uses' => '\App\Api\v2\Controllers\VersionController@index', 'middleware' => ['simpleauth']]);

    Route::get('testcase', ['uses' => '\App\Api\v2\Controllers\TestCasesController@show', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\v2\Controllers\TestCasesController@profiles', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::post('testcase/start', ['uses' => '\App\Api\v2\Controllers\TestCasesController@start', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::delete('testcase/stop', ['uses' => '\App\Api\v2\Controllers\TestCasesController@stop', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('testcase/status', ['uses' => '\App\Api\v2\Controllers\TestCasesController@status', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::get('testsuites', ['uses' => '\App\Api\v2\Controllers\TestSuitesController@index', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('testsuites/{suiteId}/testcases', ['uses' => '\App\Api\v2\Controllers\TestSuitesController@testcases', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::get('profiles/{profile}', ['uses' => '\App\Api\v2\Controllers\ProfilesController@show', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::post('transactions', ['uses' => '\App\Api\v2\Controllers\TransactionsController@create', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

    Route::post('products', ['uses' => '\App\Api\v2\Controllers\ProductsController@create', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test', 'organisation.subscriptions.product_type']]);
    Route::get('products', ['uses' => '\App\Api\v2\Controllers\ProductsController@get', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('products/{productId}/features', ['uses' => '\App\Api\v2\Controllers\ProductsController@listFeatures', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test', 'post.product.exist']]);
    Route::post('products/{productId}/features', ['uses' => '\App\Api\v2\Controllers\ProductsController@saveFeatures', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test', 'post.product.exist']]);

    //test plans
    Route::get('testplans', ['uses' => '\App\Api\v2\Controllers\TestPlansController@index', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);
    Route::get('testplans/{testPlanId}/testcases', ['uses' => '\App\Api\v2\Controllers\TestPlansController@testcases', 'middleware' => ['simpleauth', 'organisation.member', 'organisation.can_test']]);

});

Route::group(['middleware' => ['web']], function () {

    Route::get('laravel-product/{productId}', 'ProductsController@view');
    Route::group(['middleware' => ['auth']], function () {
        Route::get('laravel-product/{productId}/edit', 'ProductsController@edit');
        Route::post('laravel-product/{productId}', 'ProductsController@update');
        Route::delete('laravel-product/{productId}', 'ProductsController@destroy');
        Route::get('laravel-my-products', 'ProductsController@index');
    });

    Route::get('laravel-test-suite/{testSuiteId}', 'TestSuitesController@view');
    Route::get('laravel-test-suite/{testSuiteId}/edit', 'TestSuitesController@edit');

    Route::get('laravel-test-case/{testCaseId}', 'TestCasesController@view');
    Route::get('laravel-test-case/{testCaseId}/edit', 'TestCasesController@edit');

    Route::get('contact-us', 'ContactUsController@index');

    Route::get('contact-us', 'ContactUsController@index');
    Route::post('contact-us', 'ContactUsController@send');
    Route::get('savepost/{optionId}', 'PostController@save');

    Route::group(['middleware' => ['auth']], function () {
        Route::resource('testingdetails', 'TestingDetailsController',
            ['only' => ['store', 'index']]);
        Route::get('testingdetails/{transaction}/output/', 'TestingDetailsController@output');
        Route::get('testingdetails/{transaction}/reason/', 'TestingDetailsController@reason');
        Route::get('testingdetails/{transaction}/screen-captures/', 'TestingDetailsController@screenCaptures');
        Route::get('testingdetails/{transaction}/transaction-reason/', 'TestingDetailsController@transactionReason');
        Route::get('testingdetails/{transaction}/logs', 'TestingDetailsController@logs');
    });

    Route::get('sso/{key}', '\App\Http\Controllers\Auth\AuthController@sso');

    Route::get('communities', 'CommunitiesController@index');


    /**
     * Articles
     */
    //only community admin can create / edit / delete articles
    Route::group(['prefix' => 'articles', 'middleware' => ['auth', 'community.admin']], function () {
        Route::get('{community}/create', 'CommunityArticlesController@create');
        Route::get('{community}/{article}/edit', 'CommunityArticlesController@edit');
        Route::patch('{community}/{article}', 'CommunityArticlesController@update');
        Route::post('{community}', 'CommunityArticlesController@store');
        Route::delete('{community}/{article}/{attachmentId}', 'CommunityArticlesController@destroyattachment');
    });

    Route::group(['prefix' => 'articles', 'middleware' => ['auth', 'community.user']], function () {
        Route::get('{community}/{article}', 'CommunityArticlesController@show');
    });

    /**
     * Downloads
     */
    Route::group(['prefix' => 'downloads', 'middleware' => ['community.user']], function () {
        Route::get('{community}/getfile/{download}', 'CommunityDownloadsController@getfile');
    });

    //only admin can create / edit / delete attachments
    Route::group(['prefix' => 'downloads', 'middleware' => ['community.admin']], function () {
        Route::get('{community}/edit/{download}', 'CommunityDownloadsController@edit');
        Route::delete('{community}/{download}', 'CommunityDownloadsController@destroy');
        Route::patch('{community}/{download}', 'CommunityDownloadsController@update');
        Route::post('{community}', 'CommunityDownloadsController@store');
    });

    /**
     * Profiles
     */
    Route::group(['middleware' => ['community.user']], function () {

        Route::get('communityprofiles/{community}/viewprofile/{profileId}', 'ProfilesController@viewprofile');

        Route::get('profiletypes/{community}/downloadprofiletype/{profileTypeId}', 'ProfileTypeController@downloadprofiletype');
        Route::get('profiletypes/{community}/viewprofiletype/{profileTypeId}', 'ProfileTypeController@viewprofiletype');

    });

    Route::group(['middleware' => ['community.admin']], function () {
        Route::delete('communityprofiles/{community}/{profileId}', 'ProfilesController@destroy');
        Route::get('communityprofiles/{community}/edit/{profileId}/{profileTypeId}', 'ProfilesController@edit');
        Route::get('communityprofiles/{community}/create', 'ProfilesController@create');
        Route::post('communityprofiles/{community}/{profileId}', 'ProfilesController@update');
        Route::post('communityprofiles/{community}/', 'ProfilesController@save');
     });

    Route::group(['prefix' => 'profiletypes', 'middleware' => ['community.admin']], function () {
        Route::get('{community}/edit/{profileTypeId}', 'ProfileTypeController@edit');
        Route::post('{community}/', 'ProfileTypeController@store');
        Route::delete('{community}/{profileTypeId}', 'ProfileTypeController@destroy');
    });

    /**
     * Community
     */
    Route::group(['middleware' => ['auth']], function () {

        //any use can request community membership
        Route::post('membership/{community}/request', 'CommunityMembershipController@requestMembership');
    });

     Route::group(['middleware' => ['auth']], function () {
        //any use can request community membership
        Route::post('membership/{community}/request', 'CommunityMembershipController@requestMembership');
    });

    Route::group(['middleware' => ['wordpress.super_admin']], function () {
        //only wordpress super admin can create new community
        Route::get('communities/create/', 'CommunitiesController@create');
        Route::post('communities', 'CommunitiesController@store');
    });


    Route::get('communities/{community}/{action?}/{forumSlug?}', 'CommunitiesController@show');
    //community members routes
    Route::group(['middleware' => ['community.user']], function () {
        //view community pages
//        Route::get('communities/{community}/{action?}', 'CommunitiesController@show');
        Route::delete('membership/{community}/leave', 'CommunityMembershipController@leave');
    });

    //only community admin can update / delete community
    Route::group(['middleware' => ['community.admin']], function () {

        Route::patch('communities/{community}', 'CommunitiesController@update');
        Route::delete('communities/{community}', 'CommunitiesController@destroy');

        Route::post('communities/{community}/getjson', 'CommunitiesController@generateJson');

        Route::post('membership/{community}/reject', 'CommunityMembershipController@rejectUser');
        Route::post('membership/{community}/accept', 'CommunityMembershipController@acceptUser');

    });

    //support users / admins actions
    Route::group(['middleware' => ['community.mod']], function () {
        Route::post('membership/{community}/changerole', 'CommunityMembershipController@changeRole');
        Route::post('membership/{community}/invite', 'CommunityMembershipController@inviteUser');
        Route::post('communities/{community}/approve_organisation', 'CommunitiesController@approveOrganisation');
        Route::post('communities/{community}/approve_product', 'CommunitiesController@approveProduct');

        Route::get('communitysurveys/{community}/surveyresults', 'CommunitiesController@surveysList');
        Route::post('communitysurveys/{community}/surveyresults', 'CommunitiesController@saveSurveysLinks');
    });

    //only community admin can create  test data backups
    Route::group(['middleware' => ['community.admin']], function () {
        Route::post('communities/{community}/backup', 'CommunitiesController@backupTestData');
    });

    /**
     * Forums
     */
     Route::group(['middleware' => ['community.user']], function () {
         Route::post('forums/{community}', 'CommunityForumController@addThread');
         Route::patch('forums/{community}/{threadId}', 'CommunityForumController@updateThread');
         Route::get('/forums/{community}/edit/{threadId}', 'CommunityForumController@editThread');
         Route::delete('forums/{community}/{threadId}', 'CommunityForumController@deleteThread');


         Route::delete('forums/{community}/post/{postId}', 'CommunityForumController@deletePost');
         Route::get('/forums/{community}/editpost/{postId}', 'CommunityForumController@editThreadPost');
         Route::patch('/forums/{community}/post/{postId}', 'CommunityForumController@updateThreadPost');
         Route::post('forums/{community}/{threadSlug}', 'CommunityForumController@addThreadPost');
     });

    Route::get('search-results', 'SiteSearchController@index');
    Route::get('search-results/logs-list', 'SiteSearchController@entries');
    Route::get('search-results/download', 'SiteSearchController@download');
    Route::get('search-results/filters', 'SiteSearchController@filters');

    Route::get('products-and-services', 'RegistrySearchController@index');
    Route::get('products-and-services/logs-list', 'RegistrySearchController@entries');
    Route::get('products-and-services/download', 'RegistrySearchController@download');
    Route::get('products-and-services/filters', 'RegistrySearchController@filters');

     Route::group(['middleware' => ['auth', 'wordpress.super_admin']], function () {
        Route::delete('search-results/{entryId}', 'SiteSearchController@delete');
        Route::delete('products-and-services/{entryId}', 'RegistrySearchController@delete');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('test-suite-coverage', 'TestPlansController@index');
        Route::get('/testplan/create/{suiteId}', 'TestPlansController@create');
        Route::get('/testplan/{testPlanId}/edit/', 'TestPlansController@edit');
        Route::get('/testplan/{testPlanId}/claim/', 'TestPlansController@claim');
        Route::get('/testplan/{testPlanId}/view/{testCaseId}', 'TestPlansController@view');
        Route::post('/testplan', 'TestPlansController@store');
        Route::post('/testplan/{testPlanId}/exclude/{testCaseId}', 'TestPlansController@exclude');
        Route::post('/testplan/{testPlanId}', 'TestPlansController@update');
        Route::delete('/testplan/{planid}', 'TestPlansController@destroy');

        Route::post('/transactions/{transactionId}/updateauditrecord', 'TransactionsController@updateauditrecord');
        Route::get('/transactions/{transactionId}/explanation-logs', 'TransactionsController@explanationLogs');
        Route::post('/transactions/{transactionId}/explanation-logs/create', 'TransactionsController@addExplanationLog');
        Route::get('/transactions/filters', 'TransactionsController@filters');
        Route::get('/transactions/transactions-list', 'TransactionsController@transactionsList');

        Route::post('/transactions/update-transactions', 'TransactionsController@updateTransactions');

        Route::delete('/transactions/batch-delete', 'TransactionsController@batchDelete');
        Route::post('/transactions/bulk-audit', 'TransactionsController@bulkAudit');

        Route::get('verify-requests', 'VerifyRequestsController@index');
        Route::get('verify-requests/update-list', 'VerifyRequestsController@updateList');
        Route::get('verify-requests/{testSuiteId}/create/{productId?}/{testPlanId?}', 'VerifyRequestsController@create');

        Route::get('verify-requests/{testSuiteId}/resolve/{verifyRequestId}', 'VerifyRequestsController@resolvePopup');
        Route::post('verify-requests/{communityId}/resolve/{verifyRequestId}', 'VerifyRequestsController@resolve');

        Route::post('verify-requests/update-transactions', 'VerifyRequestsController@updateTransactions');

        Route::get('verify-requests/{testSuiteId}/assign/{verifyRequestId}', 'VerifyRequestsController@assignPopup');
        Route::post('verify-requests/{testSuiteId}/assign/{verifyRequestId}', 'VerifyRequestsController@assign');

        Route::get('verify-requests/{testSuiteId}/accept/{verifyRequestId}', 'VerifyRequestsController@acceptPopup');
        Route::post('verify-requests/{testSuiteId}/accept/{verifyRequestId}', 'VerifyRequestsController@accept');

        Route::get('verify-requests/{testSuiteId}/unassign/{verifyRequestId}', 'VerifyRequestsController@unassignPopup');
        Route::post('verify-requests/{testSuiteId}/unassign/{verifyRequestId}', 'VerifyRequestsController@unassign');

        Route::post('verify-requests', 'VerifyRequestsController@store');
        Route::delete('verify-requests/{requestId}', 'VerifyRequestsController@delete');

        Route::get('my-transaction-log', 'TransactionsController@index');
        Route::get('verify-requests/{communitySlug}/image-viewer/{verifyRequestId}/{transactionId}', 'VerifyRequestsController@imageViewerPopup');
        Route::get('verify-requests/{communitySlug}/transactions-image-viewer/{transactionId}', 'VerifyRequestsController@transactionsImageViewerPopup');
        Route::post('verify-requests/{communitySlug}/update-image-transaction/{verifyRequestId}/{transactionId}', 'VerifyRequestsController@updateTransaction');
    });


    Route::group(['middleware' => ['auth', 'wordpress.super_admin']], function () {
        Route::get('api-logs', 'ApiLogsController@index');
        Route::get('api-logs/filters', 'ApiLogsController@filters');
        Route::get('api-logs/logs-list', 'ApiLogsController@logsList');
        Route::get('api-logs/{logId}/request', 'ApiLogsController@requestData');
        Route::get('api-logs/{logId}/response', 'ApiLogsController@responseData');

        Route::get('api-logs/{logId}/download-request', 'ApiLogsController@downloadRequest');
        Route::get('api-logs/{logId}/download-response', 'ApiLogsController@downloadResponse');
    });

     Route::group(['middleware' => ['auth', 'wordpress.super_admin']], function () {
        Route::get('test-outcome-logs', 'TransactionChangeLogController@index');
        Route::get('test-outcome-logs/filters', 'TransactionChangeLogController@filters');
        Route::get('test-outcome-logs/logs-list', 'TransactionChangeLogController@logsList');
    });
});
