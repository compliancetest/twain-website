<?php

Route::group(array('prefix' => 'api/v1'), function () {

    Route::post('echo', ['uses' => '\App\Api\Controllers\EchoController@index']);

    Route::get('version', ['uses' => '\App\Api\Controllers\VersionController@index', 'middleware' => ['simpleauth']]);

    Route::get('testcase', ['uses' => '\App\Api\Controllers\TestCasesController@show', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\Controllers\TestCasesController@profiles', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::post('testcase/start', ['uses' => '\App\Api\Controllers\TestCasesController@start', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::delete('testcase/stop', ['uses' => '\App\Api\Controllers\TestCasesController@stop', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('testcase/status', ['uses' => '\App\Api\Controllers\TestCasesController@status', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::get('testsuites', ['uses' => '\App\Api\Controllers\TestSuitesController@index', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('testsuites/{suiteId}/testcases', ['uses' => '\App\Api\Controllers\TestSuitesController@testcases', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::get('profiles/{profile}', ['uses' => '\App\Api\Controllers\ProfilesController@show', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::post('transactions', ['uses' => '\App\Api\Controllers\TransactionsController@create', 'middleware' => ['simpleauth', 'organisation.member']]);

    Route::post('products', ['uses' => '\App\Api\Controllers\ProductsController@create', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('products', ['uses' => '\App\Api\Controllers\ProductsController@get', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('products/{productId}/features', ['uses' => '\App\Api\Controllers\ProductsController@listFeatures', 'middleware' => ['simpleauth', 'organisation.member', 'post.product.exist']]);
    Route::post('products/{productId}/features', ['uses' => '\App\Api\Controllers\ProductsController@saveFeatures', 'middleware' => ['simpleauth', 'organisation.member', 'post.product.exist']]);

    //test plans
    Route::get('testplans', ['uses' => '\App\Api\Controllers\TestPlansController@index', 'middleware' => ['simpleauth', 'organisation.member']]);
    Route::get('testplans/{testPlanId}/testcases', ['uses' => '\App\Api\Controllers\TestPlansController@testcases', 'middleware' => ['simpleauth', 'organisation.member']]);

});

Route::group(['middleware' => ['web']], function () {

    Route::resource('testingdetails', 'TestingDetailsController',
        ['only' => ['store',  'index']]);
    Route::get('testingdetails/{transaction}/output', 'TestingDetailsController@output');
    Route::get('testingdetails/{transaction}/reason', 'TestingDetailsController@reason');
    Route::get('testingdetails/{transaction}/logs', 'TestingDetailsController@logs');

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

        //any registered user can create community
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
    });
});
