<?php

Route::group(['middleware' => ['web']], function () {

    Route::get('communities', 'CommunitiesController@index');


    Route::group(['middleware' => ['wordpress.admin']], function () {

        Route::get('communities/create/', 'CommunitiesController@create');

        Route::get('communities/edit/{community}/step/{step}/', 'CommunitiesController@edit');

        Route::post('communities', 'CommunitiesController@store');

        Route::patch('communities/{slug}', 'CommunitiesController@update');
        Route::delete('communities/{slug}', 'CommunitiesController@destroy');
    });

    Route::get('communities/{slug}/{action?}', 'CommunitiesController@show');


    /**
     * Community popups
     */
    Route::group(['middleware' => ['wordpress.auth']], function () {
        Route::get('communities/popups/{slug}/join/{acceptedterms?}', 'CommunityMembershipController@join');
        Route::get('communities/popups/{slug}/terms', 'CommunitiesController@terms');

        Route::get('communities/popups/{slug}/leave', 'CommunityMembershipController@confirmLeave');
        Route::delete('communities/popups/{slug}/leave', 'CommunityMembershipController@leave');

        Route::post('membership/{community}/request', 'CommunityMembershipController@requestMembership');

        Route::post('communities/{community}/getjson', 'CommunitiesController@generateJson');


    });
});
