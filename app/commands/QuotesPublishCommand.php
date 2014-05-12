<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

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
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
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

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Get the number of quotes to publish
		$nbQuotes = is_null($this->argument('nb_quotes')) ? Config::get('app.quotes.nbQuotesToPublishPerDay') : $this->argument('nb_quotes');

		// Get the quotes that will be published today
		$quotes = Quote::pending()->orderAscending()->take($nbQuotes)->with('user')->get();
		$arrayUsers = array();
		$quotes->each(function($quote)
		{
			// Save the quote in storage
			$quote->approved = 2;
			$quote->save();

			$arrayUsers[] = $quote->user;

			$quoteArray = array('quote' => $quote->toArray());

			// Log this info
			$this->info("Published quote #".$quote->id);
			Log::info("Published quote #".$quote->id, array('quote' => $quoteArray));

			// Send an email to the author
			Mail::send('emails.quotes.published', $quoteArray, function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quotePublishedSubjectEmail'));
			});
		});

		// Update number of published quotes in cache
		if (Cache::has(Quote::$cacheNameNumberPublished))
			Cache::increment(Quote::$cacheNameNumberPublished, $nbQuotes);

		// We need to forget pages of quotes that are stored in cache
		// where the published quotes should be displayed
		$nbPages = ceil($nbQuotes / Quote::$nbQuotesPerPage);
		for ($i = 1; $i <= $nbPages; $i++)
			Cache::forget(Quote::$cacheNameQuotesPage.$i);

		// We forgot EVERY published quotes stored in cache for every user
		// that has published a quote this time
		$expiresAt = Carbon::now()->addMinutes(10);
		foreach ($arrayUsers as $user)
		{
			// Update in cache the number of published quotes for the user
			$nbQuotesPublishedForUser = Cache::remember(
				User::$cacheNameForNumberQuotesPublished.$user->id,
				$expiresAt,
				function() use ($user)
			{
				return Quote::forUser($user)
					->published()
					->count();
			});
			$nbPagesQuotesPublished = ceil($nbQuotesPublishedForUser / User::$nbQuotesPerPage);

			// Forgot every page
			for($i = 1; $i <= $nbPagesQuotesPublished; $i++)
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
