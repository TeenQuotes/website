<?php
/*
|--------------------------------------------------------------------------
| Patterns
|--------------------------------------------------------------------------
|
*/
Route::pattern('quote_id', '[0-9]+');
Route::pattern('country_id', '[0-9]+');
Route::pattern('story_id', '[0-9]+');
Route::pattern('comment_id', '[0-9]+');
Route::pattern('user_id', '[a-zA-Z0-9_]+');
Route::pattern('quote_approved_type', 'waiting|refused|pending|published');
Route::pattern('random', 'random');

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
*/
Route::group(['domain' => Config::get('app.domainAPI'), 'before' => 'oauth|session.remove', 'prefix' => 'v1', 'namespace' => 'TeenQuotes\Api\V1\Controllers'], function()
{
	// Comments
	Route::get('comments', ['uses' => 'CommentsController@index']);
	Route::post('comments/{quote_id}', ['uses' => 'CommentsController@store']);
	Route::delete('comments/{comment_id}', ['uses' => 'CommentsController@destroy']);
	Route::get('comments/{comment_id}', ['uses' => 'CommentsController@show']);

	// Countries
	Route::get('countries/{country_id?}', ['uses' => 'CountriesController@getCountry']);		

	// Favorite quotes
	Route::post('favorites/{quote_id}', ['uses' => 'FavQuotesController@postFavorite']);
	Route::delete('favorites/{quote_id}', ['uses' => 'FavQuotesController@deleteFavorite']);

	// Password
	Route::post('password/remind', ['uses' => 'PasswordController@postRemind']);
	Route::post('password/reset', ['uses' => 'PasswordController@postReset']);

	// Quotes
	Route::post('quotes', ['uses' => 'QuotesController@postStoreQuote']);
	Route::get('quotes/{quote_id}', ['uses' => 'QuotesController@getSingleQuote']);
	Route::get('quotes/{random?}', ['uses' => 'QuotesController@indexQuotes']);
	Route::get('quotes/favorites/{user_id?}', ['uses' => 'QuotesController@indexFavoritesQuotes']);
	Route::get('quotes/{quote_approved_type}/{user_id}', ['uses' => 'QuotesController@indexByApprovedQuotes']);
	Route::get('quotes/search/{query}', ['uses' => 'QuotesController@getSearch']);

	// Users
	Route::delete('users',['uses' => 'UsersController@deleteUsers']);
	Route::post('users', ['uses' => 'UsersController@postUsers']);
	Route::get('users', ['uses' => 'UsersController@getUsers']);
	Route::put('users/profile', ['uses' => 'UsersController@putProfile']);
	Route::get('users/{user_id}', ['uses' => 'UsersController@getSingleUser']);
	Route::put('users/password', ['uses' => 'UsersController@putPassword']);
	Route::put('users/settings', ['uses' => 'UsersController@putSettings']);
	Route::get('users/search/{query}', ['uses' => 'UsersController@getSearch']);
	
	// Search
	Route::get('search/{query}', ['uses' => 'SearchController@getSearch']);

	// Stories
	Route::get('stories', ['uses' => 'StoriesController@index']);
	Route::post('stories', ['uses' => 'StoriesController@store']);
	Route::get('stories/{story_id}', ['uses' => 'StoriesController@show']);
});