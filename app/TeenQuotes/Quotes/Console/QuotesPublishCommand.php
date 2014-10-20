<?php namespace TeenQuotes\Quotes\Console;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Users\Models\User;

class QuotesPublishCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'quotes:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish quotes for today.';

	/**
	 * Users that have published quotes
	 * @var array
	 */
	protected $users = [];

	/**
	 * Effective number of quotes published today
	 * @var int
	 */
	protected $nbQuotesPublished = 0;

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(QuoteRepository $quoteRepo)
	{
		parent::__construct();

		$this->quoteRepo = $quoteRepo;
	}

	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler
			->daily()
			->hours(11)
			->minutes(0);
	}

	/**
	 * Choose the environment(s) where the command should run
	 * @return array Array of environments' name
	 */
	public function environment()
	{
		return ['production'];
	}

	private function getNbQuotesArgument()
	{
		if (is_null($this->argument('nb_quotes')))
			return Config::get('app.quotes.nbQuotesToPublishPerDay');
		else 
			return $this->argument('nb_quotes');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Get the quotes that will be published today
		$quotes = $this->quoteRepo->lastPendingQuotes($this->getNbQuotesArgument());

		// Remember the effective number of published quotes
		$this->nbQuotesPublished = $quotes->count();

		$quotes->each(function($quote)
		{
			// Save the quote in storage
			$this->quoteRepo->updateApproved($quote->id, Quote::PUBLISHED);

			$this->users[] = $quote->user;

			// Log this info
			$this->info("Published quote #".$quote->id);
			Log::info("Published quote #".$quote->id, ['quote' => $quote->toArray()]);

			// Send an email to the author via SMTP
			new MailSwitcher('smtp');
			Mail::send('emails.quotes.published', compact('quote'), function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quotePublishedSubjectEmail'));
			});
		});

		$this->updateNumberPublishedQuotes();

		$this->forgetPagesStoredInCache();

		$this->forgetPublishedQuotesPagesForUser();
	}

	/**
	 * Update number of published quotes in cache
	 */
	private function updateNumberPublishedQuotes()
	{
		if (Cache::has(Quote::$cacheNameNumberPublished))
			Cache::increment(Quote::$cacheNameNumberPublished, $this->nbQuotesPublished);
	}
	
	/**
	 * We need to forget pages of quotes that are stored in cache
	 * where the published quotes should be displayed
	 */
	private function forgetPagesStoredInCache()
	{
		$nbPages = ceil($this->nbQuotesPublished / Config::get('app.quotes.nbQuotesPerPage'));
		
		for ($i = 1; $i <= $nbPages; $i++) {
			Cache::forget(Quote::$cacheNameQuotesAPIPage.$i);
		}
	}
	
	/**
	 * We forgot EVERY published quotes stored in cache for every user
	 * that has published a quote this time
	 */
	private function forgetPublishedQuotesPagesForUser()
	{
		foreach ($this->users as $user)
		{
			$nbQuotesPublishedForUser = $user->getPublishedQuotesCount();
			$nbPagesQuotesPublished = ceil($nbQuotesPublishedForUser / Config::get('app.users.nbQuotesPerPage'));

			// Forgot every page
			for ($i = 1; $i <= $nbPagesQuotesPublished; $i++)
				Cache::forget(User::$cacheNameForPublished.$user->id.'_'.$i);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('nb_quotes', InputArgument::OPTIONAL, 'The number of quotes to publish.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
