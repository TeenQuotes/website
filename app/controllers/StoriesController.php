<?php

class StoriesController extends BaseController {
	
	public function index()
	{
		// Retrieve stories
		$stories = Story::with('user')->orderDescending()->paginate(Config::get('app.stories.nbStoriesPerPage'));

		// Round to nearest thousand
		$nbQuotes = number_format(round(Quote::nbQuotesPublished(), -3), 0 , '.' , ',' );
		
		$data = [
			'pageTitle'       => Lang::get('stories.pageTitleIndex'),
			'pageDescription' => Lang::get('stories.pageDescriptionIndex'),
			'heroText'        => Lang::get('stories.heroText', ['nb' => $nbQuotes]),
			'stories'         => $stories,
			'tellUsYourStory' => Lang::get('stories.storiesTellTitle').'.',
			'mustBeLogged'    => Lang::get('stories.mustBeLogged'),
			'heroHide'        => false,
		];

		return View::make('stories.index', $data);
	}

	public function show($id)
	{
		$story = Story::where('id', '=', $id)->with('user')->first();

		if (is_null($story) OR $story->count() == 0)
			throw new StoryNotFoundException();

		$data = [
			'pageTitle'       => Lang::get('stories.pageTitleIndex'),
			'pageDescription' => Lang::get('stories.pageDescriptionIndex'),
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

			// Create the story
			$story = new Story;
			$story->represent_txt = $data['represent_txt'];
			$story->frequence_txt = $data['frequence_txt'];
			$story->user_id = Auth::id();
			$story->save();

			return Redirect::route('stories')->with('success', Lang::get('stories.storyAddedSuccessfull', array('login' => Auth::user()->login)));
		}

		// Something went wrong
		return Redirect::route('stories')->withErrors($validator)->withInput(Input::all());
	}
}