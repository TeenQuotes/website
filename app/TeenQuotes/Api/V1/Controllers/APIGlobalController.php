<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Api\V1\Controllers;

use Auth;
use BaseController;
use Input;
use LucaDegasperi\OAuth2Server\Facades\AuthorizationServerFacade as AuthorizationServer;
use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade as ResourceServer;
use TeenQuotes\Api\V1\Interfaces\PageBuilderInterface;
use TeenQuotes\Comments\Repositories\CommentRepository;
use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Newsletters\NewslettersManager;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Settings\Repositories\SettingRepository;
use TeenQuotes\Stories\Repositories\StoryRepository;
use TeenQuotes\Tags\Repositories\TagRepository;
use TeenQuotes\Users\Repositories\UserRepository;
use URL;

class APIGlobalController extends BaseController implements PageBuilderInterface
{
    /**
     * @var \TeenQuotes\Countries\Repositories\CountryRepository
     */
    protected $countryRepo;

    /**
     * @var \TeenQuotes\Comments\Repositories\CommentRepository
     */
    protected $commentRepo;

    /**
     * @var \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    protected $favQuoteRepo;

    /**
     * @var \TeenQuotes\Newsletters\Repositories\NewsletterRepository
     */
    protected $newsletterRepo;

    /**
     * @var \TeenQuotes\Newsletters\NewslettersManager
     */
    protected $newslettersManager;

    /**
     * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
     */
    protected $quoteRepo;

    /**
     * @var \TeenQuotes\Settings\Repositories\SettingRepository
     */
    protected $settingRepo;

    /**
     * @var \TeenQuotes\Stories\Repositories\StoryRepository
     */
    protected $storyRepo;

    /**
     * @var \TeenQuotes\Tags\Repositories\TagRepository
     */
    protected $tagRepo;

    /**
     * @var \TeenQuotes\Users\Repositories\UserRepository
     */
    protected $userRepo;

    /**
     * @var \TeenQuotes\Api\V1\Interfaces\PageBuilderInterface
     */
    protected $pageBuilder;

    public function __construct(
        CommentRepository $commentRepo, CountryRepository $countryRepo,
        FavoriteQuoteRepository $favQuoteRepo, NewsletterRepository $newsletterRepo,
        NewslettersManager $newslettersManager, QuoteRepository $quoteRepo,
        SettingRepository $settingRepo, StoryRepository $storyRepo,
        TagRepository $tagRepo, UserRepository $userRepo, PageBuilderInterface $pageBuilder)
    {
        $this->commentRepo        = $commentRepo;
        $this->countryRepo        = $countryRepo;
        $this->favQuoteRepo       = $favQuoteRepo;
        $this->newsletterRepo     = $newsletterRepo;
        $this->newslettersManager = $newslettersManager;
        $this->quoteRepo          = $quoteRepo;
        $this->settingRepo        = $settingRepo;
        $this->storyRepo          = $storyRepo;
        $this->tagRepo            = $tagRepo;
        $this->userRepo           = $userRepo;
        $this->pageBuilder        = $pageBuilder;

        $this->bootstrap();
    }

    /**
     * Bootstrap things we need to do just after the constructor
     * has been called.
     */
    protected function bootstrap()
    {
    }

    /**
     * @see \TeenQuotes\Api\V1\Interfaces\PageBuilderInterface
     */
    public function buildPagesArray($page, $pagesize, $totalPages, $url, $getParams)
    {
        return $this->pageBuilder->buildPagesArray($page, $pagesize, $totalPages, $url, $getParams);
    }

    /**
     * Display the welcome message at the root of the API.
     *
     * @return \TeenQuotes\Http\Facades\Response
     */
    public function showWelcome()
    {
        return Response::json([
            'status'            => 'You have arrived',
            'message'           => 'Welcome to the Teen Quotes API',
            'version'           => '1.0alpha',
            'url_documentation' => 'https://developers.teen-quotes.com',
            'contact'           => 'antoine.augusti@teen-quotes.com',
        ], 200);
    }

    public function postOauth()
    {
        return AuthorizationServer::performAccessTokenFlow();
    }

    /**
     * Paginate content for the API.
     *
     * @param int                            $page         The current page number
     * @param int                            $pagesize     The number of items per page
     * @param int                            $totalContent The total number of items for the search
     * @param \Illuminate\Support\Collection $content      The content
     * @param string                         $contentName  The name of the content. Example: quotes|users
     *
     * @return array Keys: total_<ressource>, total_pages, page, pagesize, url,
     *               has_next_page, has_previous_page[, next_page, previous_page]
     */
    public function paginateContent($page, $pagesize, $totalContent, $content, $contentName = 'quotes')
    {
        $totalPages = ceil($totalContent / $pagesize);

        $data = [
            $contentName          => $content,
            'total_'.$contentName => $totalContent,
            'total_pages'         => $totalPages,
            'page'                => (int) $page,
            'pagesize'            => (int) $pagesize,
            'url'                 => URL::current(),
        ];

        $getParams = null;
        if (Input::has('quote')) {
            $getParams = '&quote=true';
        }

        // Get information about previous and next pages
        $pagesArray = $this->buildPagesArray($page, $pagesize, $totalPages, $data['url'], $getParams);

        return array_merge($data, $pagesArray);
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getPage()
    {
        return max(1, Input::get('page', 1));
    }

    /**
     * Retrieve the authenticated user from the website or through OAuth.
     *
     * @return \TeenQuotes\Models\Users\User The user object
     */
    protected function retrieveUser()
    {
        // Get the user from OAuth 2
        if (ResourceServer::getOwnerId()) {
            return $this->userRepo->getById(ResourceServer::getOwnerId());
        }

        // Get the logged in user
        return Auth::user();
    }

    /**
     * Determine if a collection contains no results.
     *
     * @param null|\Illuminate\Support\Collection $content
     *
     * @return bool
     */
    protected function isNotFound($content)
    {
        return is_null($content) or empty($content) or $content->count() == 0;
    }
}
