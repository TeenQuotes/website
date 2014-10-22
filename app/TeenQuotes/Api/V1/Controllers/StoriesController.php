<?php namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Stories\Models\Story;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Exceptions\ApiNotFoundException;
class StoriesController extends APIGlobalController {

	public function index()
	{
		$page = $this->getPage();
		$pagesize = Input::get('pagesize', Config::get('app.stories.nbStoriesPerPage'));

		// Get stories
		$content = $this->storyRepo->index($page, $pagesize);

		// Handle no stories found
		if (is_null($content) OR $content->count() == 0)
			throw new ApiNotFoundException('stories');
			

		$data = self::paginateContent($page, $pagesize, $this->storyRepo->total(), $content, 'stories');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function show($story_id)
	{
		$story = $this->storyRepo->findById($story_id);

		// Handle not found
		if (is_null($story))
			return Response::json([
				'status' => 'story_not_found',
				'error'  => "The story #".$story_id." was not found.",
			], 404);
					
		return Response::json($story, 200, [], JSON_NUMERIC_CHECK);
	}

	public function store($doValidation = true)
	{
		$user = $this->retrieveUser();
		$represent_txt = Input::get('represent_txt');
		$frequence_txt = Input::get('frequence_txt');

		if ($doValidation) {	
			
			// Validate represent_txt, frequence_txt
			foreach (array_keys(Story::$rules) as $value) {
				$validator = Validator::make(compact($value), [$value => Story::$rules[$value]]);
				if ($validator->fails())
					return Response::json([
						'status' => 'wrong_'.$value,
						'error' => $validator->messages()->first($value)
					], 400);
			}
		}

		// Store the new story
		$story = $this->storyRepo->create($user, $represent_txt, $frequence_txt);

		return Response::json($story, 201, [], JSON_NUMERIC_CHECK);
	}
}