<?php

Route::group(array('prefix' => 'api/v1'), function () {
    Route::get('testcase', ['uses' => '\App\Api\Controllers\TestCasesController@show', 'middleware' => 'simpleauth']);
    Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\Controllers\TestCasesController@profiles', 'middleware' => 'simpleauth']);
    Route::get('profiles/{profile}', ['uses' => '\App\Api\Controllers\ProfilesController@show', 'middleware' => 'simpleauth']);

    Route::post('transactions', ['uses' => '\App\Api\Controllers\TransactionsController@create', 'middleware' => 'simpleauth']);

});

Route::group(['middleware' => ['web']], function () {

    Route::resource('testingdetails', 'TestingDetailsController',
        ['only' => ['store', 'update', 'index']]);

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
        //any use can request community membership
        Route::delete('communityprofiles/{community}/{profileId}', 'ProfilesController@destroy');
        Route::post('communityprofiles/{community}/copy/{profileId}', 'ProfilesController@copy');
        Route::get('communityprofiles/{community}/viewprofile/{profileId}', 'ProfilesController@viewprofile');
        Route::get('communityprofiles/{community}/edit/{profileId}/{profileTypeId}', 'ProfilesController@edit');

        Route::get('communityprofiles/{community}/viewprofiletype/{profileTypeId}', 'ProfilesController@viewprofiletype');
        Route::get('communityprofiles/{community}/downloadprofiletype/{profileTypeId}', 'ProfilesController@downloadprofiletype');
        Route::patch('communityprofiles/{community}/{profileId}', 'ProfilesController@update');
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

    Route::get('communities/{community}/{action?}', 'CommunitiesController@show');
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
        Route::post('membership/{community}/changerole', 'CommunityMembershipController@changeRole');

    });
});
