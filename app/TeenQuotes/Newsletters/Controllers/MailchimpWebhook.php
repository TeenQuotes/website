<?php namespace TeenQuotes\Newsletters\Controllers;

use BaseController, Config, Input, InvalidArgumentException, Response;
use TeenQuotes\Newsletters\NewsletterList;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Users\Repositories\UserRepository;

class MailchimpWebhook extends BaseController {

	/**
	 * @var TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	private $newsletterRepo;

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	/**
	 * @var TeenQuotes\Newsletters\NewsletterList
	 */
	private $newsletterList;

	public function __construct(UserRepository $userRepo, NewsletterRepository $newsletterRepo,
								NewsletterList $newsletterList)
	{
		$this->userRepo       = $userRepo;
		$this->newsletterRepo = $newsletterRepo;
		$this->newsletterList = $newsletterList;
	}

	public function listen()
	{
		$this->checkKey(Input::get('key'));

		$type = Input::get('type');

		switch ($type)
		{
			case 'unsubscribe':
				$this->unsubscribe(Input::get('data'));
				break;

			case 'upemail':
				$this->changeEmail(Input::get('data'));
				break;
		}

		return Response::make('DONE', 200);
	}

	private function unsubscribe($data)
	{
		$type = $this->getTypeFromListId($data['list_id']);

		$user = $this->userRepo->getByLogin($data['merges']['LOGIN']);

		if (! is_null($user))
			$this->newsletterRepo->deleteForUserAndType($user, $type);
	}

	private function changeEmail($data)
	{
		$oldEmail = $data['old_email'];
		$newEmail = $data['new_email'];

		$user = $this->userRepo->getByEmail($oldEmail);

		if (! is_null($user))
			$this->userRepo->updateEmail($user, $newEmail);
	}

	private function getTypeFromListId($listId)
	{
		return str_replace('Newsletter', '', $this->newsletterList->getNameFromListId($listId));
	}

	private function checkKey($key)
	{
		if ($key != Config::get('services.mailchimp.secret'))
			throw new InvalidArgumentException("The secret key is not valid. Got ".$key);
	}
}