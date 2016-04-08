<?php

Route::group(array('prefix' => 'api/v1'), function () {
    Route::get('testcase', ['uses' => '\App\Api\Controllers\TestCasesController@show', 'middleware' => 'simpleauth']);
    Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\Controllers\TestCasesController@profiles', 'middleware' => 'simpleauth']);
    Route::get('profiles/{profile}', ['uses' => '\App\Api\Controllers\ProfilesController@show', 'middleware' => 'simpleauth']);

    Route::post('transactions', ['uses' => '\App\Api\Controllers\TransactionsController@create', 'middleware' => 'simpleauth']);

});

Route::group(['middleware' => ['web']], function () {
    Route::resource('communities', 'CommunitiesController');
    Route::resource('testingdetails', 'TestingDetailsController',
        ['only' => ['store', 'update', 'index']]);


    Route::get('sso/{key}', '\App\Http\Controllers\Auth\AuthController@sso');

    Route::get('communities', 'CommunitiesController@index');

    Route::group(['prefix' => 'downloads', 'middleware' => ['auth', 'community.admin']], function () {
        Route::get('{community}/create', 'CommunityDownloadsController@create');

        Route::get('{community}/edit/{download}', 'CommunityDownloadsController@edit');

        Route::delete('{community}/{download}', 'CommunityDownloadsController@destroy');

        Route::patch('{community}/{download}', 'CommunityDownloadsController@update');

        Route::post('{community}', 'CommunityDownloadsController@store');
    });

    Route::group(['prefix' => 'downloads', 'middleware' => ['auth', 'community.user']], function () {

        Route::get('{community}/agreement/{download}', 'CommunityDownloadsController@agreement');

        Route::get('{community}/getfile/{download}', 'CommunityDownloadsController@getfile');

        Route::get('{community}/confirmdelete/{download}', 'CommunityDownloadsController@confirmDelete');

    });


    Route::group(['middleware' => ['auth']], function () {
        Route::get('communities/popups/{community}/join/{acceptedterms?}', 'CommunityMembershipController@join');
        Route::get('communities/popups/{community}/terms', 'CommunitiesController@terms');
        Route::post('membership/{community}/request', 'CommunityMembershipController@requestMembership');

        //user should be community member to leave it
        Route::group(['middleware' => ['community.user']], function () {
            Route::get('communities/popups/{community}/leave', 'CommunityMembershipController@confirmLeave');
            Route::delete('communities/popups/{community}/leave', 'CommunityMembershipController@leave');
        });
        Route::post('communities/{community}/getjson', 'CommunitiesController@generateJson');
    });

    Route::group(['middleware' => ['auth']], function () {

        //any registered user can create community
        Route::get('communities/create/', 'CommunitiesController@create');
        Route::post('communities', 'CommunitiesController@store');

        Route::group(['middleware' => ['community.admin']], function () {

            Route::get('communities/edit/{community}/step/{step}/', 'CommunitiesController@edit');
            Route::patch('communities/{community}', 'CommunitiesController@update');
            Route::delete('communities/{community}', 'CommunitiesController@destroy');

        });
    });

    Route::get('communities/{community}/{action?}', 'CommunitiesController@show');

});
