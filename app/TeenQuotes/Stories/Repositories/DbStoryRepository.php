<?php

namespace TeenQuotes\Stories\Repositories;

use TeenQuotes\Stories\Models\Story;
use TeenQuotes\Users\Models\User;

class DbStoryRepository implements StoryRepository
{
    /**
     * Retrieve a story by its ID.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Stories\Models\Story
     */
    public function findById($id)
    {
        return Story::where('id', '=', $id)
            ->withSmallUser()
            ->first();
    }

    /**
     * List stories.
     *
     * @param int $page
     * @param int $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index($page, $pagesize)
    {
        return Story::withSmallUser()
            ->orderDescending()
            ->take($pagesize)
            ->skip($this->computeSkip($page, $pagesize))
            ->get();
    }

    /**
     * Get the total number of stories.
     *
     * @return int
     */
    public function total()
    {
        return Story::count();
    }

    /**
     * Create a story.
     *
     * @param \TeenQuotes\Users\Models\User $u
     * @param string                        $represent_txt
     * @param string                        $frequence_txt
     *
     * @return \TeenQuotes\Stories\Models\Story
     */
    public function create(User $u, $represent_txt, $frequence_txt)
    {
        $story = new Story();
        $story->represent_txt = $represent_txt;
        $story->frequence_txt = $frequence_txt;
        $story->user_id = $u->id;
        $story->save();

        return $story;
    }

    private function computeSkip($page, $pagesize)
    {
        return $pagesize * ($page - 1);
    }
}
