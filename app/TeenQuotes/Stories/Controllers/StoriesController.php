<?php namespace TeenQuotes\Stories\Controllers;

use BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Paginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use TeenQuotes\Exceptions\StoryNotFoundException;
use TeenQuotes\Stories\Models\Story;

class StoriesController extends BaseController {
	
	/**
	 * The API controller
	 * @var TeenQuotes\Api\V1\Controllers\StoriesController
	 */
	private $api;

	public function __construct()
	{
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\StoriesController');
	}

	public function index()
	{
		// Retrieve stories from the API
		$apiResponse = $this->api->index();
		if ($this->responseIsNotFound($apiResponse))
			throw new StoryNotFoundException;
		
		// Extract the stories collection
		$response = $apiResponse->getOriginalData();
		$stories = $response['stories'];

		$totalQuotes = $stories->first()->present()->totalQuotes;

		$data = [
			'heroHide'        => false,
			'heroText'        => Lang::get('stories.heroText', ['nb' => $totalQuotes]),
			'mustBeLogged'    => Lang::get('stories.mustBeLogged'),
			'pageDescription' => Lang::get('stories.pageDescriptionIndex'),
			'pageTitle'       => Lang::get('stories.pageTitleIndex'),
			'paginator'       => Paginator::make($stories->toArray(), $response['total_stories'], $response['pagesize']),
			'stories'         => $stories,
			'tellUsYourStory' => Lang::get('stories.storiesTellTitle').'.',
		];

		return View::make('stories.index', $data);
	}

	public function show($id)
	{
		// Call the API
		$apiResponse = $this->api->show($id);
		if ($this->responseIsNotFound($apiResponse))
			throw new StoryNotFoundException;

		$story = $apiResponse->getOriginalData();

		$data = [
			'pageTitle'       => Lang::get('stories.pageTitleShow', ['nb' => $story->id]),
			'pageDescription' => $story->present()->pageDescription,
			'story'           => $story,
			'goBack'          => Lang::get('layout.goBack'),
			'storyTitle'      => Lang::get('stories.storyTitle'),
			'heroHide'        => true,
		];

		return View::make('stories.show', $data);
	}

	public function store()
	{
		$validator = Validator::make(Input::only(['represent_txt', 'frequence_txt']), Story::$rules);
		
		// Check if the form validates with success
		if ($validator->passes()) {

			// Call the API - skip the API validator
			$response = $this->api->store(false);
			if ($response->getStatusCode() == 201)
				return Redirect::route('stories')->with('success', Lang::get('stories.storyAddedSuccessfull', ['login' => Auth::user()->login]));
			
			return Redirect::route('stories')->withErrors($validator)->withInput(Input::all());
		}

		// Something went wrong
		return Redirect::route('stories')->withErrors($validator)->withInput(Input::all());
	}
}