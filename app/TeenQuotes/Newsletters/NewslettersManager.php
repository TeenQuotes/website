<?php namespace TeenQuotes\Newsletters;

use App;
use Illuminate\Support\Collection;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Newsletters\NewsletterList;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Users\Models\User;

class NewslettersManager {

	/**
	 * @var \TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	private $newslettersRepo;

	/**
	 * @var \TeenQuotes\Newsletters\NewsletterList
	 */
	private $newslettersList;

	public function __construct(NewsletterRepository $newslettersRepo, NewsletterList $newslettersList)
	{
		$this->newslettersRepo = $newslettersRepo;
		$this->newslettersList = $newslettersList;
	}

	public function createForUserAndType(User $user, $type)
	{
		$this->newslettersRepo->createForUserAndType($user, $type);

		if ($this->shouldCallAPI())
			$this->newslettersList->subscribeTo($this->getListNameFromType($type), $user);
	}

	public function deleteForUserAndType(User $u, $type)
	{
		if ($this->shouldCallAPI())
			$this->newslettersList->unsubscribeFrom($this->getListNameFromType($type), $u);

		return $this->newslettersRepo->deleteForUserAndType($u, $type);
	}

	public function deleteForUsers(Collection $users)
	{
		if ($this->shouldCallAPI())
		{
			foreach (Newsletter::getPossibleTypes() as $type)
				$this->newslettersList->unsubscribeUsersFrom($this->getListNameFromType($type), $users);
		}

		return $this->newslettersRepo->deleteForUsers($users);
	}

	private function shouldCallAPI()
	{
		return App::environment() == 'production';
	}

	private function getListNameFromType($type)
	{
		return $type.'Newsletter';
	}
}