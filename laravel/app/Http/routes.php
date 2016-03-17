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


$api = app('Dingo\Api\Routing\Router');

$api->version('v1',['middleware' => 'api.auth'], function ($api) {

        $api->get('testcases/{id}', 'App\Api\Controllers\TestCasesController@show');
});
Route::group(['middleware' => ['web']], function () {
    Route::resource('communities', 'CommunitiesController');
});
