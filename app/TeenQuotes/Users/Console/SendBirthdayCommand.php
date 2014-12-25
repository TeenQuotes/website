<?php namespace TeenQuotes\Users\Console;

use Lang, Log;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Users\Repositories\UserRepository;

class SendBirthdayCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'birthday:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Whish happy birthday to the concerned users.';

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	/**
	 * @var TeenQuotes\Mail\UserMailer
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
		return $scheduler
			->daily()
			->hours(12)
			->minutes(30);
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
		$users = $this->userRepo->birthdayToday();

		$users->each(function($user)
		{
			// Log this info
			$this->log("Wishing happy birthday to ".$user->login." - ".$user->email);

			$this->userMailer->send('emails.events.birthday',
				$user,
				compact('user'),
				Lang::get('email.happyBirthdaySubjectEmail')
			);
		});
	}

	private function log($string)
	{
		$this->info($string);
		Log::info($string);
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
