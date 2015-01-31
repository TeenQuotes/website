<?php namespace TeenQuotes\Users\Console;

use Carbon, Log;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Symfony\Component\Console\Input\InputArgument;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Users\Repositories\UserRepository;

class EmailSpecialEventCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'emailevent:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send an email to all users for a special event.';

	/**
	 * Allowed newsletter types
	 * @var array
	 */
	private $possibleEvents = ['christmas', 'newyear'];

	/**
	 * @var \TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	/**
	 * @var \TeenQuotes\Mail\UserMailer
	 */
	private $userMailer;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(UserRepository $userRepo, UserMailer $userMailer)
	{
		parent::__construct();

		$this->userRepo = $userRepo;
		$this->userMailer = $userMailer;
	}

	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return [
			$scheduler
				->args(['newyear'])
				->months(1)
				->daysOfTheMonth(1)
				->hours(4)
				->minutes(15),

			$scheduler
				->args(['christmas'])
				->months(12)
				->daysOfTheMonth(25)
				->hours(4)
				->minutes(15),
		];
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
	 */
	public function fire()
	{
		if ($this->eventTypeIsValid())
		{
			$event = $this->getEvent();

			// Retrieve users that have logged in the last year
			$allUsers = $this->userRepo->getLoggedInSince(Carbon::now()->subYear(1), 1, 2000);

			$delay = Carbon::now()->addMinutes(5);
			$i = 1;
			// We will send 60 emails every hour
			foreach ($allUsers->chunk(60) as $users)
			{
				$driver = $this->determineMailDriver($i);

				$users->each(function($user) use($event, $delay, $driver)
				{
					// Log this info
					$this->log("Scheduled email for event ".$event." to ".$user->login." - ".$user->email." for ".$delay->toDateTimeString());

					// Add the email to the queue
					$this->userMailer->sendEvent($event, $user, $driver, $delay);
				});

				$i++;
				$delay->addHour();
			}
		}
	}

	private function determineMailDriver($i)
	{
		if ($i > 1000)
			return null;

		return 'smtp';
	}

	private function log($string)
	{
		$this->info($string);
		Log::info($string);
	}

	private function eventTypeIsValid()
	{
		$event = $this->getEvent();

		if (is_null($event) or ! in_array($event, $this->possibleEvents))
		{
			$this->error('Wrong type of event! Got '.$event.'. Possible values are: '.$this->presentPossibleEvents().'.');
			return false;
		}

		return true;
	}

	private function getEvent()
	{
		return $this->argument('event');
	}

	private function presentPossibleEvents()
	{
		return implode('|', $this->possibleEvents);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['event', InputArgument::REQUIRED, 'The name of the event. '.$this->presentPossibleEvents()],
		];
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
