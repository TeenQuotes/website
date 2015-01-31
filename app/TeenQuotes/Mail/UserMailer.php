<?php namespace TeenQuotes\Mail;

use App, Carbon, Lang;
use Illuminate\Config\Repository as Config;
use Illuminate\Queue\QueueManager as Queue;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Repositories\UserRepository;

class UserMailer {

	/**
	 * @var \Illuminate\Config\Repository
	 */
	private $config;

	/**
	 * @var \Illuminate\Mail\Mailer
	 */
	private $mail;

	/**
	 * @var \Illuminate\Queue\QueueManager
	 */
	private $queue;

	/**
	 * @var \TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	public function __construct(Config $config, Queue $queue, UserRepository $userRepo)
	{
		$this->config   = $config;
		$this->queue    = $queue;
		$this->userRepo = $userRepo;
	}

	/**
	 * Send a mail to a user
	 *
	 * @param string $viewName The name of the view
	 * @param TeenQuotes\Users\Models\User $user
	 * @param array $data Data to pass the email view
	 * @param string $subject The subject of the email
	 * @param string $driver The name of the driver that we will use to send the email
	 */
	public function send($viewName, User $user, $data, $subject, $driver = null)
	{
		$this->switchDriverIfNeeded($driver);

		// Send the email
		$this->mail->send($viewName, $data, function ($m) use($user, $subject)
		{
			$m->to($user->email, $user->login)->subject($subject);
		});
	}

	/**
	 * Send a delayed mail to a user
	 *
	 * @param string $viewName The name of the view
	 * @param TeenQuotes\Users\Models\User $user
	 * @param array $data Data to pass the email view
	 * @param string $subject The subject of the email
	 * @param string $driver The name of the driver that we will use to send the email
	 * @param DateTime|int $delay
	 */
	public function sendLater($viewName, User $user, $data, $subject, $driver = null, $delay = 0)
	{
		$data = compact('viewName', 'user', 'data', 'subject', 'driver');

		// Queue the email
		$this->queue->later($delay, get_class($this).'@dispatchToSend', $data);
	}

	/**
	 * Send an email with a job
	 *
	 * @param \Illuminate\Queue\Jobs\Job $job
	 * @param array $data Required keys: viewName, user, data, subject and driver.
	 */
	public function dispatchToSend($job, $data)
	{
		extract($data);
		$this->send($viewName, $this->getUserFromId($user['id']), $data, $subject, $driver);
	}

	/**
	 * Tell an author of a quote that a comment was posted on one of
	 * its quote
	 *
	 * @param  \TeenQuotes\Users\Models\User  	$author The author of the quote
	 * @param  \TeenQuotes\Quotes\Models\Quote  $quote The quote
	 */
	public function warnAuthorAboutCommentPosted(User $author, Quote $quote)
	{
		$subject = Lang::get('comments.commentAddedSubjectEmail', ['id' => $quote->id]);

		$this->send('emails.comments.posted',
			$author,
			compact('quote'),
			$subject
		);
	}

	/**
	 * Tell a user that he was subscribed from our newsletters
	 *
	 * @param  \TeenQuotes\Users\Models\User $user
	 */
	public function unsubscribeUserFromNewsletter(User $user)
	{
		$this->send('emails.newsletters.unsubscribe',
			$user,
			compact('user'),
			Lang::get('email.unsubscribeNewsletterSubject')
		);
	}

	/**
	 * Tell the author of a quote that its quote was published
	 *
	 * @param  \TeenQuotes\Quotes\Models\Quote $quote
	 */
	public function tellQuoteWasPublished(Quote $quote)
	{
		$this->send('emails.quotes.published',
			$quote->user, // The author of the quote
			compact('quote'),
			Lang::get('quotes.quotePublishedSubjectEmail')
		);
	}

	/**
	 * Wish happy birthday to a user
	 *
	 * @param  \TeenQuotes\Users\Models\User $user
	 */
	public function wishHappyBirthday(User $user)
	{
		$this->send('emails.events.birthday',
			$user,
			compact('user'),
			Lang::get('email.happyBirthdaySubjectEmail')
		);
	}

	/**
	 * Send the welcome email to a user
	 *
	 * @param  \TeenQuotes\Users\Models\User $user
	 */
	public function sendWelcome(User $user)
	{
		// Data for the view
		$data = [
			'login' => $user->login,
			'email' => $user->email,
		];

		$subject = Lang::get('auth.subjectWelcomeEmail', ['login' => $data['login']]);

		$this->sendLater('emails.welcome',
			$user,
			$data,
			$subject,
			null, // Use the default driver
			Carbon::now()->addMinutes(10) // Defer the email
		);
	}

	/**
	 * Retrieve an user by its ID
	 *
	 * @param int $id
	 * @return TeenQuotes\Users\Models\User
	 */
	private function getUserFromId($id)
	{
		return $this->userRepo->getById($id);
	}

	private function switchDriverIfNeeded($driver)
	{
		if (is_null($driver)) $driver = $this->getCurrentDriver();

		$this->guardDriver($driver);

		// Ask to use this driver
		new MailSwitcher($driver);

		$this->registerMailDriver();
	}

	private function getCurrentDriver()
	{
		return $this->config->get('mail.driver');
	}

	private function registerMailDriver()
	{
		$this->mail = App::make('mailer');
	}

	private function guardDriver($driver)
	{
		if (! $this->isTestingEnvironment())
			MailSwitcher::guardDriver($driver);
	}

	private function isTestingEnvironment()
	{
		return in_array(App::environment(), ['testing', 'codeception', 'codeceptionMysql']);
	}

	private function getAvailableDrivers()
	{
		return MailSwitcher::getAvailableDrivers();
	}

	private function presentAvailableDrivers()
	{
		return MailSwitcher::presentAvailableDrivers();
	}
}