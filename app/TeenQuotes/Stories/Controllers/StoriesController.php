<?php namespace TeenQuotes\Stories\Controllers;

use App, Auth, BaseController, Input, Lang, Paginator, Redirect, View;
use TeenQuotes\Exceptions\StoryNotFoundException;
use Laracasts\Validation\FormValidationException;

class StoriesController extends BaseController {

	/**
	 * The API controller
	 * @var \TeenQuotes\Api\V1\Controllers\StoriesController
	 */
	private $api;

	/**
	 * @var \TeenQuotes\Stories\Validation\StoryValidator
	 */
	private $storyValidator;

	public function __construct()
	{
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\StoriesController');
		$this->storyValidator = App::make('TeenQuotes\Stories\Validation\StoryValidator');
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
		try {
			$this->storyValidator->validatePosting(Input::only(['represent_txt', 'frequence_txt']));
		}
		catch (FormValidationException $e)
		{
			return Redirect::route('stories')
				->withErrors($e->getErrors())
				->withInput(Input::all());
		}

		// Call the API - skip the API validator
		$response = $this->api->store(false);
		if ($response->getStatusCode() == 201)
			return Redirect::route('stories')
				->with('success', Lang::get('stories.storyAddedSuccessfull', ['login' => Auth::user()->login]));
	}
}