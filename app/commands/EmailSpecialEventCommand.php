<?php

use Illuminate\Console\Command;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
		return [
			$scheduler
				->args(['newyear'])
				->months(1)
				->daysOfTheMonth(1)
				->hours(12)
				->minutes(15),

			$scheduler
				->args(['christmas'])
				->months(12)
				->daysOfTheMonth(25)
				->hours(12)
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
		if ($this->eventTypeIsValid()) {		
			$event = $this->getEvent();

			$users = User::all();
			
			$users->each(function($user) use($event)
			{
				// Log this info
				$this->info("Sending email for event ".$event." to ".$user->login." - ".$user->email);
				Log::info("Sending email for event ".$event." to ".$user->login." - ".$user->email);

				// Send the email to the user
				Mail::send('emails.events.'.$event, compact('user'), function($m) use($user, $event)
				{
					$m->to($user->email, $user->login)->subject(Lang::get('email.event'.ucfirst($event).'SubjectEmail'));
				});
			});
		}
	}

	private function eventTypeIsValid()
	{
		$event = $this->getEvent();

		if (is_null($event) OR !in_array($event, $this->possibleEvents)) {
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
		return array(
			array('event', InputArgument::REQUIRED, 'The name of the event. '.$this->presentPossibleEvents()),
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
