<?php namespace TeenQuotes\Users\Repositories;

use TeenQuotes\Users\Models\ProfileVisitor;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Repositories\UserRepository;

class DbProfileVisitorRepository implements ProfileVisitorRepository {

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	public function __construct(UserRepository $userRepo)
	{
		$this->userRepo = $userRepo;
	}

	/**
	 * Tell that a user visited another user's profile
	 *
	 * @param int|TeenQuotes\Users\Models\User $visited
	 * @param int|TeenQuotes\Users\Models\User $visitor
	 */
	public function addVisitor($visited, $visitor)
	{
		$visited = $this->retrieveUser($visited);
		$visitor = $this->retrieveUser($visitor);

		$visited->visitors()->save($visitor);
	}

	/**
	 * Get visitors for a given user
	 *
	 * @param  int|TeenQuotes\Users\Models\User $u
	 * @param  int $page
	 * @param  int $pagesize
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getVisitors($u, $page, $pagesize)
	{
		$u = $this->retrieveUser($u);

		return $u->visitors()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->where(function($q)
			{
				$q->where('users.hide_profile', 0);
			})
			->latest('profile_visitors.id')
			->distinct()
			->get();
	}

	/**
	 * Get visitors' information for a given user
	 *
	 * @param  int|TeenQuotes\Users\Models\User $u
	 * @param  int $page
	 * @param  int $pagesize
	 * @return array ['login' => 'avatarURL'] array
	 */
	public function getVisitorsInfos($u, $page, $pagesize)
	{
		$collection = $this->getVisitors($u, $page, $pagesize);

		return $collection->reduce(function($result, $u)
		{
			$result[$u->login] = $u->getURLAvatarAttribute();

			return $result;
		});
	}

	/**
	 * Tells if a user has visited the profile of another user
	 *
	 * @param  int|TeenQuotes\Users\Models\User $visitor
	 * @param  int|TeenQuotes\Users\Models\User $visited
	 * @return boolean
	 */
	public function hasVisited($visitor, $visited)
	{
		$visitor = $this->retrieveUser($visitor);
		$visited = $this->retrieveUser($visited);

		return in_array($visitor->id, $visited->visitors()->select('users.*')->lists('id'));
	}

	/**
	 * Retrieves a user
	 *
	 * @param  int|TeenQuotes\Users\Models\User $u
	 * @return TeenQuotes\Users\Models\User
	 */
	private function retrieveUser($u)
	{
		if ($u instanceof User) return $u;

		return $this->userRepo->getById($u);
	}

	private function computeSkip($page, $pagesize)
	{
		return $pagesize * ($page - 1);
	}
}