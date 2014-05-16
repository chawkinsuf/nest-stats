<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'App\Controllers\Home@getIndex');

Route::controller('users', 'App\Controllers\Users');
Route::controller('graphs', 'App\Controllers\Graphs');

Route::get( 'graphs/data/{deviceId?}/{dataSetId?}', 'App\Controllers\Graphs')->where([ 'deviceId' => '[0-9]+', 'dataSetId' => '[0-9]+' ]);