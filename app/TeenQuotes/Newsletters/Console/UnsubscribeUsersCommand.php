<?php namespace TeenQuotes\Newsletters\Console;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use MandrillClient;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Users\Repositories\UserRepository;

class UnsubscribeUsersCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'newsletter:deleteUsers';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Unsubscribe users from newsletters.';

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	/**
	 * @var TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	private $newsletterRepo;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(UserRepository $userRepo, NewsletterRepository $newsletterRepo)
	{
		parent::__construct();

		$this->userRepo = $userRepo;
		$this->newsletterRepo = $newsletterRepo;
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
			->hours(17)
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
	 */
	public function fire()
	{
		// Retrieve inactive users
		$nonActiveUsers = $this->userRepo->getNonActiveHavingNewsletter();

		$hardBouncedUsers = $this->getHardBouncedUsers();

		// Merge all users that need to be unsubscribed from newsletters
		$allUsers = $nonActiveUsers->merge($hardBouncedUsers);

		// Unsubscribe these users from newsletters
		$this->newsletterRepo->deleteForUsers($allUsers->lists('id'));

		// Send an email to each user to notice them
		new MailSwitcher('smtp');
		$nonActiveUsers->each(function($user)
		{
			// Log this info
			$this->writeToLog("Unsubscribing user from newsletters: ".$user->login." - ".$user->email);

			Mail::send('emails.newsletters.unsubscribe', compact('user'), function($m) use($user)
			{
				$m->to($user->email, $user->login)->subject(Lang::get('email.unsubscribeNewsletterSubject'));
			});
		});
	}

	/**
	 * Get users that has already have been affected by an hard bounce
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	private function getHardBouncedUsers()
	{
		$users = MandrillClient::getHardBouncedUsers();

		// Delete each user from the existing rejection list
		$instance = $this;
		$users->each(function($user) use ($instance)
		{
			MandrillClient::deleteEmailFromRejection($user->email);
			
			// Log this info
			$instance->writeToLog("Removing user from the rejection list: ".$user->login." - ".$user->email);
		});

		return $users;
	}

	private function writeToLog($line)
	{
		$this->info($line);
		Log::info($line);
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
