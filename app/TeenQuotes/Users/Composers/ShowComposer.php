<?php

namespace TeenQuotes\Users\Composers;

use Auth;
use Illuminate\Database\Eloquent\Collection;
use Queue;
use TeenQuotes\Queues\Workers\ProfileVisitorWorker;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;
use TextTools;

class ShowComposer implements QuotesColorsExtractor
{
    /**
     * The section of the profile we are viewing.
     *
     * @var string
     */
    private $type;

    /**
     * The user viewed.
     *
     * @var \TeenQuotes\Users\Models\User
     */
    private $user;

    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = $view->getData();
        $this->type = $data['type'];
        $this->user = $data['user'];

        $view->with('hideAuthorQuote', $this->type == 'published');
        $view->with('commentsCount', TextTools::formatNumber($this->user->getTotalComments()));
        $view->with('addedFavCount', TextTools::formatNumber($this->user->getAddedFavCount()));
        $view->with('quotesPublishedCount', TextTools::formatNumber($this->user->getPublishedQuotesCount()));
        $view->with('favCount', TextTools::formatNumber($this->user->getFavoriteCount()));

        // Extract colors for quotes
        $view->with('colors', $this->extractAndStoreColors($data['quotes']));

        // Register the visit of the profile
        $this->registerVisit();
    }

    private function registerVisit()
    {
        if (Auth::check()) {
            $user_id = $this->user->id;
            $visitor_id = Auth::id();

            if ($user_id != $visitor_id) {
                Queue::push(ProfileVisitorWorker::class.'@viewProfile', compact('user_id', 'visitor_id'));
            }
        }
    }

    public function extractAndStoreColors($quotes)
    {
        if (!($quotes instanceof Collection)) {
            $quotes = new Collection($quotes);
        }

        $colors = [];

        switch ($this->type) {
            case 'favorites':
                $colors = Quote::storeQuotesColors($quotes->lists('id'));
                break;

            case 'published':
                $colors = Quote::storeQuotesColors($quotes->lists('id'), $this->user->getColorsQuotesPublished());
                break;
        }

        return $colors;
    }
}
