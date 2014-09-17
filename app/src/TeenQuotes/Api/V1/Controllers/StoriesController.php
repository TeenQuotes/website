<?php
namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade as ResourceServer;
use \Story;
use \User;

class StoriesController extends APIGlobalController {
	
	public function index()
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.stories.nbStoriesPerPage'));

        if ($page <= 0)
			$page = 1;

		// Number of stories to skip
        $skip = $pagesize * ($page - 1);

		$totalStories = Story::count();

        // Get stories
        $content = Story::
			withSmallUser()
			->orderDescending()
			->take($pagesize)
			->skip($skip)
			->get();

		// Handle no stories found
		if (is_null($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No stories have been found.'
			];

			return Response::json($data, 404);
		}

		$data = self::paginateContent($page, $pagesize, $totalStories, $content, 'stories');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function show($story_id)
	{
		$story = Story::where('id', '=', $story_id)
			->withSmallUser()
			->first();

		// Handle not found
		if (is_null($story)) {

			$data = [
				'status' => 'story_not_found',
				'error'  => "The story #".$story_id." was not found",
			];

			return Response::json($data, 404);
		}
		
		return Response::json($story, 200, [], JSON_NUMERIC_CHECK);
	}

	public function store($doValidation = true)
	{
		$user = $this->retrieveUser();
		$represent_txt = Input::get('represent_txt');
		$frequence_txt = Input::get('frequence_txt');

		if ($doValidation) {		
			
			// Validate represent_txt
			$validatorRepresent = Validator::make(compact('represent_txt'), ['represent_txt' => Story::$rules['represent_txt']]);
			if ($validatorRepresent->fails()) {
				$data = [
					'status' => 'wrong_represent_txt',
					'error' => $validatorRepresent->messages()->first('represent_txt')
				];

				return Response::json($data, 400);
			}

			// Validate frequence_txt
			$validatorFrequence = Validator::make(compact('frequence_txt'), ['frequence_txt' => Story::$rules['frequence_txt']]);
			if ($validatorFrequence->fails()) {
				$data = [
					'status' => 'frequence_txt',
					'error' => $validatorFrequence->messages()->first('frequence_txt')
				];

				return Response::json($data, 400);
			}
		}

		// Store the new story
		$story = new Story;
		$story->represent_txt = $represent_txt;
		$story->frequence_txt = $frequence_txt;
		$story->user_id = $user->id;
		$story->save();

		return Response::json($story, 200, [], JSON_NUMERIC_CHECK);
	}
}