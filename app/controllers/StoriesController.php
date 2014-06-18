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
}