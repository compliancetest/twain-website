<?php


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(array('prefix' => 'api/v1'), function()
    {
        Route::get('testcases/{testcaseid}/product/{productid}', ['uses' => '\App\Api\Controllers\TestCasesController@show', 'middleware' => 'simpleauth']);
        Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\Controllers\TestCasesController@profiles', 'middleware' => 'simpleauth']);

        Route::get('profiles/{profile}', ['uses' => '\App\Api\Controllers\ProfilesController@show', 'middleware' => 'simpleauth']);

    });

Route::group(['middleware' => ['web']], function () {
    Route::resource('communities', 'CommunitiesController');
});
