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

Route::get('/', array('as' => 'home', 'uses' => 'QuotesController@index'));

/* --- AUTH --- */
Route::get('signin', array('as' => 'signin', 'uses' => 'AuthController@getSignin'));
Route::get('logout', array('as' => 'logout', 'uses' => 'AuthController@getLogout'));
Route::post('signin', 'AuthController@postSignin');

/* --- USERS --- */
Route::resource('users', 'UsersController', array('only' => array('index', 'show')));

/* --- QUOTES --- */
Route::get('/random', array('as' => 'random', 'uses' => 'QuotesController@index'));
Route::resource('quotes', 'QuotesController', array('only' => array('index', 'show')));