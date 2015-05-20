<?php

namespace TeenQuotes\Stories\Repositories;

use TeenQuotes\Users\Models\User;

interface StoryRepository
{
    /**
     * Retrieve a story by its ID.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Stories\Models\Story
     */
    public function findById($id);

    /**
     * List stories.
     *
     * @param int $page
     * @param int $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index($page, $pagesize);

    /**
     * Get the total number of stories.
     *
     * @return int
     */
    public function total();

    /**
     * Create a story.
     *
     * @param \TeenQuotes\Users\Models\User $u
     * @param string                        $represent_txt
     * @param string                        $frequence_txt
     *
     * @return \TeenQuotes\Stories\Models\Story
     */
    public function create(User $u, $represent_txt, $frequence_txt);
}
