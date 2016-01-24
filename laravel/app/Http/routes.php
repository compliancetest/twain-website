<?php

Route::group(['middleware' => ['web']], function () {

    Route::get('communities', 'CommunitiesController@index');

    Route::group(['middleware' => ['wordpress.auth', 'community.admin']], function () {
        Route::get('downloads/{community}/create', 'CommunityDownloadsController@create');

        Route::get('downloads/{community}/edit/{download}', 'CommunityDownloadsController@edit');

        Route::get('downloads/{community}/agreement/{download}', 'CommunityDownloadsController@agreement');

        Route::get('downloads/{community}/getfile/{download}', 'CommunityDownloadsController@getfile');

        Route::get('downloads/{community}/confirmdelete/{download}', 'CommunityDownloadsController@confirmDelete');

        Route::delete('downloads/{community}/{download}', 'CommunityDownloadsController@destroy');

        Route::patch('downloads/{community}/{download}', 'CommunityDownloadsController@update');

        Route::post('downloads/{community}', 'CommunityDownloadsController@store');
    });


    Route::group(['middleware' => ['wordpress.auth']], function () {
        Route::get('communities/popups/{slug}/join/{acceptedterms?}', 'CommunityMembershipController@join');
        Route::get('communities/popups/{slug}/terms', 'CommunitiesController@terms');

        Route::get('communities/popups/{slug}/leave', 'CommunityMembershipController@confirmLeave');
        Route::delete('communities/popups/{slug}/leave', 'CommunityMembershipController@leave');

        Route::post('membership/{community}/request', 'CommunityMembershipController@requestMembership');

        Route::post('communities/{community}/getjson', 'CommunitiesController@generateJson');


    });

    Route::group(['middleware' => ['wordpress.admin']], function () {

        Route::get('communities/create/', 'CommunitiesController@create');

        Route::get('communities/edit/{community}/step/{step}/', 'CommunitiesController@edit');

        Route::post('communities', 'CommunitiesController@store');

        Route::patch('communities/{slug}', 'CommunitiesController@update');
        Route::delete('communities/{slug}', 'CommunitiesController@destroy');
    });

    Route::get('communities/{slug}/{action?}', 'CommunitiesController@show');

});
