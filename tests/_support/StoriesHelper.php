<?php namespace Codeception\Module;

use Codeception\Module;
use Carbon;
use Illuminate\Support\Facades\Config;

class StoriesHelper extends Module {

	public function getNbStoriesToCreate()
	{
		return $this->getNbPagesToCreate() * $this->getNbStoriesPerPage();
	}

	public function getNbPagesToCreate()
	{
		return 3;
	}

	public function getNbStoriesPerPage()
	{
		return Config::get('app.stories.nbStoriesPerPage');
	}

	/**
	 * Create some stories
	 * @param  array $overrides The key-value array used to override dummy values. If the key nb_stories is given, specifies the number of stories to create
	 * @return array The created stories
	 */
	public function createSomeStories($overrides = [])
	{
		// Create stories in the past
		$overrides['created_at'] = Carbon::now()->subMonth(1);

		$nbStories = $this->getNbStoriesToCreate();
		
		if (array_key_exists('nb_stories', $overrides)) {
			$nbStories = $overrides['nb_stories'];
			unset($overrides['nb_stories']);
		}
		
		return $this->getModule('FunctionalHelper')->insertInDatabase($nbStories, 'Story', $overrides);
	}

	public function createAStoryWithHiddenUser()
	{
		$user = $this->getModule('FunctionalHelper')->insertInDatabase(1, 'User', ['hide_profile' => 1]);

		return $this->getModule('FunctionalHelper')->insertInDatabase(1, 'Story', ['user_id' => $user->id]);
	}
}