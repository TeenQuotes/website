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

Route::get('/', function()
{
	Auth::attempt(array('login' => 'antoineaugusti', 'password' => '1234'));
	
	return Quote::published()->with('comments')->with('user')->with('favorites')->with('favorites.user')->orderBy('created_at', 'DESC')->get();
});

Route::get('/home', function()
{
	return View::make('home', array('pageTitle' => 'Hello'));
});