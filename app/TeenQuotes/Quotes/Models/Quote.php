<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Models;

use App;
use Auth;
use Easyrec;
use Laracasts\Presenter\PresentableTrait;
use Queue;
use ResourceServer;
use Session;
use TeenQuotes\Quotes\Models\Relations\QuoteTrait as QuoteRelationsTrait;
use TeenQuotes\Quotes\Models\Scopes\QuoteTrait as QuoteScopesTrait;
use TeenQuotes\Users\Models\User;
use Toloquent;

class Quote extends Toloquent
{
    use PresentableTrait, QuoteRelationsTrait, QuoteScopesTrait;

    protected $presenter = 'TeenQuotes\Quotes\Presenters\QuotePresenter';

    /**
     * Constants associated with the approved field of the quote.
     */
    const REFUSED   = -1;
    const WAITING   = 0;
    const PUBLISHED = 1;
    const PENDING   = 2;

    protected $fillable = [];

    protected $hidden = ['updated_at'];

    /**
     * Adding customs attributes to the object.
     *
     * @var array
     */
    protected $appends = ['tags_list', 'has_comments', 'total_comments', 'is_favorite', 'total_favorites'];

    /**
     * The colors that will be used for quotes on the admin page.
     *
     * @var array
     */
    public static $colors = [
        '#27ae60', '#16a085', '#d35400', '#e74c3c', '#8e44ad', '#F9690E', '#2c3e50', '#f1c40f', '#65C6BB', '#E08283',
    ];

    /**
     * @var \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    private $favQuoteRepo;

    /**
     * @var \TeenQuotes\Comments\Repositories\CommentRepository
     */
    private $commentRepo;

    /**
     * @var \TeenQuotes\Tags\Repositories\TagRepository
     */
    private $tagsRepo;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->favQuoteRepo = App::make('TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository');
        $this->commentRepo  = App::make('TeenQuotes\Comments\Repositories\CommentRepository');
        $this->tagsRepo     = App::make('TeenQuotes\Tags\Repositories\TagRepository');
    }

    /**
     * Store colors that will be use to display quotes in an associative array: quote_id => css_class_name. This array is stored in session to be used when displaying a single quote.
     *
     * @param array  $quotesIDs IDs of the quotes
     * @param string $colors    If we want to use different colors, give a string here. Example: orange|blue|red..
     *
     * @return array The associative array: quote_id => color
     */
    public static function storeQuotesColors($quotesIDs, $color = null)
    {
        $colors = [];

        // We will build an array if we have at least one quote
        if (count($quotesIDs) >= 1) {
            $func = function ($value) use ($color) {
                if (is_null($color)) {
                    return 'color-'.$value;
                } else {
                    return 'color-'.$color.'-'.$value;
                }
            };

            $colors = array_map($func, range(1, count($quotesIDs)));
            $colors = array_combine($quotesIDs, $colors);
        }

        // Store it in session
        Session::set('colors.quote', $colors);

        return $colors;
    }

    /**
     * Get the total number of comments.
     *
     * @return int
     */
    public function getTotalCommentsAttribute()
    {
        // If the quote is not published, obviously we have no comments
        if (!$this->isPublished()) {
            return 0;
        }

        return $this->commentRepo->nbCommentsForQuote($this);
    }

    /**
     * Tell if the quote has comment.
     *
     * @return bool
     */
    public function getHasFavoritesAttribute()
    {
        return $this->total_favorites > 0;
    }

    /**
     * Get the total number of favorites.
     *
     * @return int
     */
    public function getTotalFavoritesAttribute()
    {
        // If the quote is not published, obviously we have no favorites
        if (!$this->isPublished()) {
            return 0;
        }

        return $this->favQuoteRepo->nbFavoritesForQuote($this->id);
    }

    /**
     * Tell if the quote has comments.
     *
     * @return bool
     */
    public function getHasCommentsAttribute()
    {
        return $this->total_comments > 0;
    }

    /**
     * Tell if the quote is favorited for the current logged-in user.
     *
     * @return bool
     */
    public function getIsFavoriteAttribute()
    {
        return $this->isFavoriteForCurrentUser();
    }

    /**
     * Get the list of tags for the quote.
     *
     * @return array
     */
    public function getTagsListAttribute()
    {
        return $this->tagsRepo->tagsForQuote($this);
    }

    public function isFavoriteForCurrentUser()
    {
        $idUserApi = ResourceServer::getOwnerId();

        if (Auth::check() or !empty($idUserApi)) {
            $id = Auth::check() ? Auth::id() : $idUserApi;

            return $this->favQuoteRepo->isFavoriteForUserAndQuote($id, $this->id);
        }

        return false;
    }

    public function isPublished()
    {
        return $this->approved == self::PUBLISHED;
    }

    public function isPending()
    {
        return $this->approved == self::PENDING;
    }

    public function isWaiting()
    {
        return $this->approved == self::WAITING;
    }

    public function isRefused()
    {
        return $this->approved == self::REFUSED;
    }

    /**
     * Register a view action in the Easyrec recommendation engine.
     */
    public function registerViewAction()
    {
        if (!in_array(App::environment(), ['testing', 'codeception'])) {
            // Try to retrieve the ID of the user
            if (Auth::guest()) {
                $idUserApi          = ResourceServer::getOwnerId();
                $userRecommendation = !empty($idUserApi) ? $idUserApi : null;
            } else {
                $userRecommendation = Auth::id();
            }

            // Register in the recommendation system
            $data = [
                'quote_id' => $this->id,
                'user_id'  => $userRecommendation,
            ];

            Queue::push('TeenQuotes\Queues\Workers\EasyrecWorker@viewQuote', $data);
        }
    }
}
