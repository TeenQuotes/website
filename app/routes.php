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

Route::get('/', ['as' => 'home', 'uses' => 'QuotesController@index']);

/* --- AUTH --- */
Route::get('signin', ['as' => 'signin', 'uses' => 'AuthController@getSignin']);
Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
Route::post('signin', 'AuthController@postSignin');

/* --- USERS --- */
Route::get("/signup", ["as" => "signup", "before" => "guest", "uses" => "UsersController@getSignup"]);
Route::resource('users', 'UsersController', ['only' => ['index', 'show', 'store']]);

/* --- QUOTES --- */
Route::get('/random', ['as' => 'random', 'uses' => 'QuotesController@index']);
Route::get('/addquote', ['as' => 'addquote', 'before' => 'auth', 'uses' => 'QuotesController@getAddQuote']);
Route::resource('quotes', 'QuotesController', ['only' => ['index', 'show', 'store']]);