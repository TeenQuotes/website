<?php
/*
|--------------------------------------------------------------------------
| Patterns
|--------------------------------------------------------------------------
|
*/
Route::pattern('quote_id', '[0-9]+');
Route::pattern('country_id', '[0-9]+');
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
	// Countries
	Route::get('countries/{country_id?}', ['uses' => 'CountriesAPIv1Controller@getCountry']);		

	// Favorite quotes
	Route::post('favorites/{quote_id}', ['uses' => 'FavQuotesAPIv1Controller@postFavorite']);
	Route::delete('favorites/{quote_id}', ['uses' => 'FavQuotesAPIv1Controller@deleteFavorite']);

	// Quotes
	Route::post('quotes', ['uses' => 'QuotesAPIv1Controller@postStoreQuote']);
	Route::get('quotes/{quote_id}', ['uses' => 'QuotesAPIv1Controller@getSingleQuote']);
	Route::get('quotes/{random?}', ['uses' => 'QuotesAPIv1Controller@indexQuotes']);
	Route::get('quotes/favorites/{user_id?}', ['uses' => 'QuotesAPIv1Controller@indexFavoritesQuotes']);
	Route::get('quotes/{quote_approved_type}/{user_id}', ['uses' => 'QuotesAPIv1Controller@indexByApprovedQuotes']);
	Route::get('quotes/search/{query}', ['uses' => 'QuotesAPIv1Controller@getSearch']);

	// Users
	Route::post('users', ['uses' => 'UsersAPIv1Controller@postUsers']);
	Route::get('users/{user_id}', ['uses' => 'UsersAPIv1Controller@getSingleUser']);
	Route::put('users/password', ['uses' => 'UsersAPIv1Controller@putPassword']);
});