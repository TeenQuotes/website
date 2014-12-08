<?php namespace TeenQuotes\Mail;

use Mandrill as M;
use Illuminate\Support\Collection;
use TeenQuotes\Users\Repositories\UserRepository;

class Mandrill {

	/**
	 * The client for the Mandrill API
	 * @var Mandrill
	 */
	private $api;

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	public function __construct(M $api, UserRepository $userRepo)
	{
		$this->api = $api;
		$this->userRepo = $userRepo;
	}

	/**
	 * Get email addresses that have already have an hard bounce
	 * @return Illuminate\Support\Collection
	 */
	public function getHardBouncedEmails()
	{
		$result = $this->api->rejects->getList("", false);
		$collection = new Collection($result);

		$hardBounced = $collection->filter(function($a)
		{
			return $a['reason'] == 'hard-bounce';
		});

		return $hardBounced->lists('email');
	}

	/**
	 * Get users that has already have been affected by an hard bounce
	 * @return Illuminate\Support\Collection
	 */
	public function getHardBouncedUsers()
	{
		return $this->userRepo->getByEmails(
			$this->getHardBouncedEmails()
		);
	}

	/**
	 * Delete an email address from the rejection list
	 * @param  string $email
	 * @return boolean Whether the address was deleted successfully
	 */
	public function deleteEmailFromRejection($email)
	{
		$result = $this->api->rejects->delete($email);

		return $result['deleted'];
	}
}