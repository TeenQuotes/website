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
Route::group(['domain' => Config::get('app.domainAPI'), 'before' => 'oauth|session.remove', 'prefix' => 'v1'], function()
{
	// Comments
	Route::get('comments', ['uses' => 'CommentsAPIv1Controller@index']);
	Route::post('comments/{quote_id}', ['uses' => 'CommentsAPIv1Controller@store']);
	Route::delete('comments/{comment_id}', ['uses' => 'CommentsAPIv1Controller@destroy']);
	Route::get('comments/{comment_id}', ['uses' => 'CommentsAPIv1Controller@show']);

	// Countries
	Route::get('countries/{country_id?}', ['uses' => 'CountriesAPIv1Controller@getCountry']);		

	// Favorite quotes
	Route::post('favorites/{quote_id}', ['uses' => 'FavQuotesAPIv1Controller@postFavorite']);
	Route::delete('favorites/{quote_id}', ['uses' => 'FavQuotesAPIv1Controller@deleteFavorite']);

	// Password
	Route::post('password/remind', ['uses' => 'PasswordAPIv1Controller@postRemind']);
	Route::post('password/reset', ['uses' => 'PasswordAPIv1Controller@postReset']);

	// Quotes
	Route::post('quotes', ['uses' => 'QuotesAPIv1Controller@postStoreQuote']);
	Route::get('quotes/{quote_id}', ['uses' => 'QuotesAPIv1Controller@getSingleQuote']);
	Route::get('quotes/{random?}', ['uses' => 'QuotesAPIv1Controller@indexQuotes']);
	Route::get('quotes/favorites/{user_id?}', ['uses' => 'QuotesAPIv1Controller@indexFavoritesQuotes']);
	Route::get('quotes/{quote_approved_type}/{user_id}', ['uses' => 'QuotesAPIv1Controller@indexByApprovedQuotes']);
	Route::get('quotes/search/{query}', ['uses' => 'QuotesAPIv1Controller@getSearch']);

	// Users
	Route::delete('users',['uses' => 'UsersAPIv1Controller@deleteUsers']);
	Route::post('users', ['uses' => 'UsersAPIv1Controller@postUsers']);
	Route::get('users', ['uses' => 'UsersAPIv1Controller@getUsers']);
	Route::put('users/profile', ['uses' => 'UsersAPIv1Controller@putProfile']);
	Route::get('users/{user_id}', ['uses' => 'UsersAPIv1Controller@getSingleUser']);
	Route::put('users/password', ['uses' => 'UsersAPIv1Controller@putPassword']);
	Route::put('users/settings', ['uses' => 'UsersAPIv1Controller@putSettings']);
	Route::get('users/search/{query}', ['uses' => 'UsersAPIv1Controller@getSearch']);
	
	// Search
	Route::get('search/{query}', ['uses' => 'SearchAPIv1Controller@getSearch']);

	// Stories
	Route::get('stories', ['uses' => 'StoriesAPIv1Controller@index']);
	Route::post('stories', ['uses' => 'StoriesAPIv1Controller@store']);
	Route::get('stories/{story_id}', ['uses' => 'StoriesAPIv1Controller@show']);
});