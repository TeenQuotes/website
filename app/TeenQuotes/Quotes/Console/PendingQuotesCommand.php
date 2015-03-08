<?php namespace TeenQuotes\Quotes\Console;

use Carbon;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use TeenQuotes\Notifiers\AdminNotifier;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class PendingQuotesCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'quotes:pending';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get some statistics about pending quotes.';

	/**
	 * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var \TeenQuotes\Notifiers\AdminNotifier
	 */
	private $adminNotifier;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(QuoteRepository $quoteRepo, AdminNotifier $adminNotifier)
	{
		parent::__construct();

		$this->quoteRepo     = $quoteRepo;
		$this->adminNotifier = $adminNotifier;
	}

	/**
	 * When the command should run
	 *
	 * @param  \Indatus\Dispatcher\Scheduling\Schedulable
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler
			->daily()
			->hours(12)
			->minutes(0);
	}

	/**
	 * Choose the environment(s) where the command should run
	 *
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
		// Retrieve data
		$pendingToday = $this->getPendingToday();
		$totalPending = $this->getPendingAllTime();

		// Warn the administrator
		$this->sendStatsToAdministrator($pendingToday, $totalPending);
	}

	/**
	 * Send some statistics to the administrator
	 *
	 * @param  int $pendingToday The number of quotes submitted today
	 * @param  int $totalPending The number of quotes waiting moderation
	 */
	private function sendStatsToAdministrator($pendingToday, $totalPending)
	{
		$message = "Number of quotes submitted today: ".$pendingToday." Total: ".$totalPending;

		$this->adminNotifier->notify($message);
	}

	/**
	 * Get the number of quotes submitted in the last 24 hours
	 *
	 * @return int
	 */
	private function getPendingToday()
	{
		$yesterday = Carbon::now()->subDay();

		return $this->quoteRepo->countPendingQuotesSince($yesterday);
	}

	/**
	 * Get the number of quotes waiting moderation
	 *
	 * @return int
	 */
	private function getPendingAllTime()
	{
		return $this->quoteRepo->nbPending();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}
}