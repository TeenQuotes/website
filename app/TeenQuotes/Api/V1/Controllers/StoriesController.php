<?php

namespace TeenQuotes\Api\V1\Controllers;

use App;
use Config;
use Input;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;

class StoriesController extends APIGlobalController implements PaginatedContentInterface
{
    /**
     * @var \TeenQuotes\Stories\Validation\StoryValidator
     */
    private $storyValidator;

    protected function bootstrap()
    {
        $this->storyValidator = App::make('TeenQuotes\Stories\Validation\StoryValidator');
    }

    public function index()
    {
        $page = $this->getPage();
        $pagesize = $this->getPagesize();

        // Get stories
        $stories = $this->storyRepo->index($page, $pagesize);

        // Handle no stories found
        if ($this->isNotFound($stories)) {
            throw new ApiNotFoundException('stories');
        }

        $data = $this->paginateContent($page, $pagesize, $this->storyRepo->total(), $stories, 'stories');

        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function show($story_id)
    {
        $story = $this->storyRepo->findById($story_id);

        // Handle not found
        if ($this->isNotFound($story)) {
            return $this->tellStoryWasNotFound($story_id);
        }

        return Response::json($story, 200, [], JSON_NUMERIC_CHECK);
    }

    public function store($doValidation = true)
    {
        $user = $this->retrieveUser();
        $data = Input::only(['frequence_txt', 'represent_txt']);

        if ($doValidation) {
            $this->storyValidator->validatePosting($data);
        }

        // Store the new story
        $story = $this->storyRepo->create($user, $data['represent_txt'], $data['frequence_txt']);

        return Response::json($story, 201, [], JSON_NUMERIC_CHECK);
    }

    public function getPagesize()
    {
        return Input::get('pagesize', Config::get('app.stories.nbStoriesPerPage'));
    }

    /**
     * Tell that a story was not found.
     *
     * @param int $story_id
     *
     * @return \TeenQuotes\Http\Facades\Response
     */
    private function tellStoryWasNotFound($story_id)
    {
        return Response::json([
            'status' => 'story_not_found',
            'error'  => 'The story #'.$story_id.' was not found.',
        ], 404);
    }
}
