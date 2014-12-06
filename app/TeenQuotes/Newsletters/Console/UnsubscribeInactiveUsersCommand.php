<?php namespace TeenQuotes\Newsletters\Console;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Users\Repositories\UserRepository;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;

class UnsubscribeInactiveUsersCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'newsletter:deleteInactive';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Unsubscribe inactive users from newsletters.';

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
		$users = $this->userRepo->getNonActiveHavingNewsletter();

		// Unsubscribe these users from newsletters
		$this->newsletterRepo->deleteForUsers($users->lists('id'));

		// Send an email to each user to notice them
		new MailSwitcher('smtp');
		$users->each(function($user)
		{
			// Log this info
			$this->info("Unsubscribing user from newsletters: ".$user->login." - ".$user->email);
			Log::info("Unsubscribing user from newsletters: ".$user->login." - ".$user->email);

			Mail::send('emails.newsletters.unsubscribe', compact('user'), function($m) use($user)
			{
				$m->to($user->email, $user->login)->subject(Lang::get('email.unsubscribeNewsletterSubject'));
			});
		});
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
