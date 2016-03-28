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
});
