<?php

class StoriesController extends BaseController {
	
	public function index()
	{
		// Retrieve stories
		$stories = Story::withSmallUser()
			->orderDescending()
			->paginate(Config::get('app.stories.nbStoriesPerPage'));

		$totalQuotes = $stories->first()->present()->totalQuotes;

		$data = [
			'pageTitle'       => Lang::get('stories.pageTitleIndex'),
			'pageDescription' => Lang::get('stories.pageDescriptionIndex'),
			'heroText'        => Lang::get('stories.heroText', ['nb' => $totalQuotes]),
			'stories'         => $stories,
			'tellUsYourStory' => Lang::get('stories.storiesTellTitle').'.',
			'mustBeLogged'    => Lang::get('stories.mustBeLogged'),
			'heroHide'        => false,
		];

		return View::make('stories.index', $data);
	}

	public function show($id)
	{
		$story = Story::where('id', '=', $id)
			->withSmallUser()
			->first();

		if (is_null($story) OR $story->count() == 0)
			throw new StoryNotFoundException();

		$data = [
			'pageTitle'       => Lang::get('stories.pageTitleShow', ['nb' => $story->id]),
			'pageDescription' => substr($story->frequence_txt, 0, 200),
			'story'           => $story,
			'goBack'          => Lang::get('layout.goBack'),
			'storyTitle'      => Lang::get('stories.storyTitle'),
			'heroHide'        => true,
		];

		return View::make('stories.show', $data);
	}

	public function store()
	{
		$data = [
			'represent_txt' => Input::get('represent_txt'),
			'frequence_txt' => Input::get('frequence_txt'),
		];

		$validator = Validator::make($data, Story::$rules);
		
		// Check if the form validates with success
		if ($validator->passes()) {

			// Call the API - skip the API validator
			$response = App::make('TeenQuotes\Api\V1\Controllers\StoriesController')->store(false);
			if ($response->getStatusCode() == 201)
				return Redirect::route('stories')->with('success', Lang::get('stories.storyAddedSuccessfull', array('login' => Auth::user()->login)));
			
			return Redirect::route('stories')->withErrors($validator)->withInput(Input::all());
		}

		// Something went wrong
		return Redirect::route('stories')->withErrors($validator)->withInput(Input::all());
	}
}