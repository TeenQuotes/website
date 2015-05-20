<?php

namespace TeenQuotes\Stories\Presenters;

use App;
use Laracasts\Presenter\Presenter;
use Str;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class StoryPresenter extends Presenter
{
    /**
     * The diff time telling when the story was published.
     *
     * @return string
     */
    public function storyAge()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the total number of quotes published in a human readable way.
     *
     * @return string
     */
    public function totalQuotes()
    {
        $repo = App::make(QuoteRepository::class);
        $nbPublished = $repo->totalPublished();

        // Round to nearest thousand
        return number_format(round($nbPublished, -3), 0, '.', ',');
    }

    /**
     * Get the page description for a story.
     *
     * @return string
     */
    public function pageDescription()
    {
        return Str::limit($this->frequence_txt, 200);
    }
}
