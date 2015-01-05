<?php namespace TeenQuotes\Newsletters\Mailchimp;

use App;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as View;
use TeenQuotes\Newsletters\NewsletterList as NewsletterListInterface;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Repositories\UserRepository;

class NewsletterList implements NewsletterListInterface {

	/**
	 * @var Mailchimp
	 */
	protected $mailchimp;

	/**
	 * @var Illuminate\View\Factory
	 */
	protected $view;

	/**
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	protected $userRepo;

	/**
	 * @var array
	 */
	protected $lists = [
		'weeklyNewsletter' => 'd22415c1ea',
		'dailyNewsletter'  => 'f9e52170f4',
	];

	function __construct(View $view, Config $config, UserRepository $userRepo)
	{
		$this->mailchimp = App::make('MailchimpClient');
		$this->view = $view;
		$this->config = $config;
		$this->userRepo = $userRepo;
	}

	/**
	 * Subscribe a user to a Mailchimp list
	 *
	 * @param string $listName
	 * @param TeenQuotes\Users\Models\User $email
	 * @return mixed
	 */
	public function subscribeTo($listName, User $user)
	{
		return $this->mailchimp->lists->subscribe(
			$this->getListIdFromName($listName),
			['email' => $user->email],
			$this->getMergeVarsForUser($user), // Merge vars
			'html', // Email type
			false, // Require double opt in?
			true // Update existing customers?
		);
	}

	/**
	 * Subscribe multiple users to a newsletter
	 *
	 * @param  string $listName
	 * @param  Illuminate\Support\Collection $collection A collection of users
	 * @return mixed
	 */
	public function subscribeUsersTo($listName, Collection $collection)
	{
		$batch = $this->extractBatchSubscribe($collection);

		return $this->mailchimp->lists->batchSubscribe(
			$this->getListIdFromName($listName),
			$batch,
			false, // Require double opt in?
			true, // Update existing customers.
			true // Replace interest
		);
	}

	/**
	 * Unsubscribe a user from a Mailchimp list
	 *
	 * @param string $listName
	 * @param TeenQuotes\Users\Models\User $email
	 * @return mixed
	 */
	public function unsubscribeFrom($listName, User $user)
	{
		return $this->mailchimp->lists->unsubscribe(
			$this->getListIdFromName($listName),
			['email' => $user->email],
			false, // Delete the member permanently
			false, // Send goodbye email?
			false // Send unsubscribe notification email?
		);
	}

	/**
	 * Unsubscribe multiple users from a newsletter
	 *
	 * @param  string $listName
	 * @param  Illuminate\Support\Collection $collection A collection of users
	 * @return mixed
	 */
	public function unsubscribeUsersFrom($listName, Collection $collection)
	{
		$batch = $this->extractBatchUnsubscribe($collection);

		return $this->mailchimp->lists->batchUnsubscribe(
			$this->getListIdFromName($listName),
			$batch,
			true, // Delete member
			false, // Send goodbye
			false // Send notify
		);
	}

	/**
	 * Send a campaign to a list
	 *
	 * @param  string $listName
	 * @param  string $subject
	 * @param  string $toName
	 * @param  string $viewName
	 * @param  array $viewData
	 * @return mixed
	 */
	public function sendCampaign($listName, $subject, $toName, $viewName, $viewData)
	{
		$from = $this->config->get('mail.from');

		$options = [
			'list_id'       => $this->getListIdFromName($listName),
			'subject'       => $subject,
			'to_name'       => $toName,
			'from_email'    => $from['address'],
			'from_name'     => $from['name'],
			'generate_text' => true, // Auto generate the plain text version
			'inline_css'    => true, // Automatically inline CSS
		];

		$html = $this->view->make($viewName, $viewData)->render();
		$content = compact('html');

		$campaign = $this->mailchimp->campaigns->create('regular', $options, $content);

		return $this->mailchimp->campaigns->send($campaign['id']);
	}

	/**
	 * Get users who unsubscribed from a list
	 *
	 * @param  string $listName
	 * @return Illuminate\Support\Collection $collection A collection of users
	 */
	public function getUnsubscribesFromList($listName)
	{
		$emails = $this->getEmailsUnsubscribedFromList($listName);

		return $this->userRepo->getByEmails($emails);
	}

	/**
	 * Get a mailing list ID from its user-friendly name
	 * @param  string $name
	 * @return string
	 */
	public function getListIdFromName($name)
	{
		return $this->lists[$name];
	}

	/**
	 * Get the user-friendly name of a mailing list from its ID
	 * @param  string $listId
	 * @return string
	 */
	public function getNameFromListId($listId)
	{
		return array_search($listId, $this->lists);
	}

	private function getEmailsUnsubscribedFromList($listName)
	{
		$emails = [];

		$out = $this->mailchimp->lists->members(
			$this->getListIdFromName($listName),
			'unsubscribed', // Status: subscribed|unsubscribed|cleaned
			null // Options
		);

		foreach ($out['data'] as $user)
			$emails[] = $user['email'];

		return $emails;
	}

	private function extractBatchSubscribe(Collection $c)
	{
		$batch = [];

		foreach ($c as $user)
		{
			$out['email'] = ['email' => $user->email];
			$out['email_type'] = 'html';
			$out['merge_vars'] = $this->getMergeVarsForUser($user);

			$batch[] = $out;
		}

		return $batch;
	}

	private function extractBatchUnsubscribe(Collection $c)
	{
		$batch = [];

		foreach ($c as $user)
		{
			$batch[] = ['email' => $user->email];
		}

		return $batch;
	}

	private function getMergeVarsForUser(User $u)
	{
		return [
			'LOGIN' => $u->login,
		];
	}
}