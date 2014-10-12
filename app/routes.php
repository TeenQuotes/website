<?php

/* --- MAIN WEBSITE --- */
Route::group(['domain' => Config::get('app.domain')], function()
{
	/* --- SEARCH --- */
	Route::get('search', ['as' => 'search.form', 'uses' => 'SearchController@showForm']);
	Route::post('search', ['as' => 'search.dispatcher', 'uses' => 'SearchController@dispatcher']);
	Route::get('search/{query}', ['as' => 'search.results', 'uses' => 'SearchController@getResults']);
});