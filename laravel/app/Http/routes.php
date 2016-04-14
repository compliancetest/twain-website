<?php

Route::group(array('prefix' => 'api/v1'), function () {

    Route::post('echo', ['uses' => '\App\Api\Controllers\EchoController@index']);

    Route::get('testcase', ['uses' => '\App\Api\Controllers\TestCasesController@show', 'middleware' => 'simpleauth']);
    Route::get('testcases/{testcaseid}/profiles/', ['uses' => '\App\Api\Controllers\TestCasesController@profiles', 'middleware' => 'simpleauth']);

    Route::post('testcase/start', ['uses' => '\App\Api\Controllers\TestCasesController@start', 'middleware' => 'simpleauth']);
    Route::delete('testcase/stop', ['uses' => '\App\Api\Controllers\TestCasesController@stop', 'middleware' => 'simpleauth']);
    Route::get('testcase/status', ['uses' => '\App\Api\Controllers\TestCasesController@status', 'middleware' => 'simpleauth']);

    Route::get('testsuites', ['uses' => '\App\Api\Controllers\TestSuitesController@index', 'middleware' => 'simpleauth']);
    Route::get('testsuites/{suiteId}/testcases', ['uses' => '\App\Api\Controllers\TestSuitesController@testcases', 'middleware' => 'simpleauth']);

    Route::get('profiles/{profile}', ['uses' => '\App\Api\Controllers\ProfilesController@show', 'middleware' => 'simpleauth']);

    Route::post('transactions', ['uses' => '\App\Api\Controllers\TransactionsController@create', 'middleware' => 'simpleauth']);

});

Route::group(['middleware' => ['web']], function () {
    Route::resource('communities', 'CommunitiesController');
    Route::resource('testingdetails', 'TestingDetailsController',
                ['only' => ['store', 'update', 'index']]);
    Route::get('testingdetails/{id}/output',  'TestingDetailsController@output');
});
