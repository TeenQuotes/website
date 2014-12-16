<?php namespace TeenQuotes\Api\V1\Controllers;

use Auth, BaseController, Input, URL;
use LucaDegasperi\OAuth2Server\Facades\AuthorizationServerFacade as AuthorizationServer;
use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade as ResourceServer;
use TeenQuotes\Comments\Repositories\CommentRepository;
use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Newsletters\NewslettersManager;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Settings\Repositories\SettingRepository;
use TeenQuotes\Stories\Repositories\StoryRepository;
use TeenQuotes\Users\Repositories\UserRepository;

class APIGlobalController extends BaseController {
	/**
	 * @var TeenQuotes\Countries\Repositories\CountryRepository
	 */
	protected $countryRepo;

	/**
	 * @var TeenQuotes\Comments\Repositories\CommentRepository
	 */
	protected $commentRepo;

	/**
	 * @var TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
	 */
	protected $favQuoteRepo;

	/**
	 * @var TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	protected $newsletterRepo;
	
	/**
	 * @var TeenQuotes\Newsletters\NewslettersManager
	 */
	protected $newslettersManager;

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	protected $quoteRepo;

	/**
	 * @var TeenQuotes\Settings\Repositories\SettingRepository
	 */
	protected $settingRepo;

	/**
	 * @var TeenQuotes\Stories\Repositories\StoryRepository
	 */
	protected $storyRepo;

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	protected $userRepo;

	public function __construct(
		CommentRepository $commentRepo, CountryRepository $countryRepo,
		FavoriteQuoteRepository $favQuoteRepo, NewsletterRepository $newsletterRepo,
		NewslettersManager $newslettersManager, QuoteRepository $quoteRepo,
		SettingRepository $settingRepo, StoryRepository $storyRepo,
		UserRepository $userRepo)
	{
		$this->commentRepo        = $commentRepo;
		$this->countryRepo        = $countryRepo;
		$this->favQuoteRepo       = $favQuoteRepo;
		$this->newsletterRepo     = $newsletterRepo;
		$this->newslettersManager = $newslettersManager;
		$this->quoteRepo          = $quoteRepo;
		$this->settingRepo        = $settingRepo;
		$this->storyRepo          = $storyRepo;
		$this->userRepo           = $userRepo;

		$this->bootstrap();
	}

	protected function bootstrap() {}

	public function showWelcome()
	{
		return Response::json([
			'status'            => 'You have arrived',
			'message'           => 'Welcome to the Teen Quotes API',
			'version'           => '1.0alpha',
			'url_documentation' => 'https://github.com/TeenQuotes/api-documentation',
			'contact'           => 'antoine.augusti@teen-quotes.com',
		], 200);
	}

	public function postOauth()
	{
		return AuthorizationServer::performAccessTokenFlow();
	}

	/**
	 * Paginate content for the API after a search for example
	 * @param  int $page The current page number
	 * @param  int $pagesize The number of items per page
	 * @param  int $totalContent The total number of items for the search
	 * @param  Collection $content The content we searched for
	 * @param  string $contentName The name of the content. Example: quotes|users
	 * @return array A big array
	 */
	public static function paginateContent($page, $pagesize, $totalContent, $content, $contentName = 'quotes')
	{
		$totalPages = ceil($totalContent / $pagesize);
		
		$data = [
			$contentName          => $content,
			'total_'.$contentName => $totalContent,
			'total_pages'         => $totalPages,
			'page'                => (int) $page,
			'pagesize'            => (int) $pagesize,
			'url'                 => URL::current()
		];
		
		$additionalGet = null;
		if (Input::has('quote'))
			$additionalGet = '&quote=true';

		// Add next page URL
		if ($page < $totalPages) {
			$data['has_next_page'] = true;
			$data['next_page'] = $data['url'].'?page='.($page + 1).'&pagesize='.$pagesize.$additionalGet;
		}
		else
			$data['has_next_page'] = false;

		// Add previous page URL
		if ($page >= 2) {
			$data['has_previous_page'] = true;
			$data['previous_page'] = $data['url'].'?page='.($page - 1).'&pagesize='.$pagesize.$additionalGet;
		}
		else
			$data['has_previous_page'] = false;

		return $data;
	}

	public function getPage()
	{
		return max(1, Input::get('page', 1));
	}

	/**
	 * Retrieve the authenticated user from the website or via the API
	 * @return \User The user object
	 */
	public function retrieveUser()
	{
		return ResourceServer::getOwnerId() ? $this->userRepo->getById(ResourceServer::getOwnerId()) : Auth::user();
	}
}