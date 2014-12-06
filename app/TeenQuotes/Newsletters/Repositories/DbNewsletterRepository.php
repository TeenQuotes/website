<?php namespace TeenQuotes\Newsletters\Repositories;

use InvalidArgumentException;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Users\Models\User;

class DbNewsletterRepository implements NewsletterRepository {
	
	/**
	 * Tells if a user if subscribed to a newsletter type
	 * @param  TeenQuotes\Users\Models\User   $u    The given user
	 * @param  string $type The newsletter's type
	 * @return bool
	 */
	public function userIsSubscribedToNewsletterType(User $u, $type)
	{
		$this->guardType($type);

		return Newsletter::forUser($u)
			->type($type)
			->count() > 0;
	}

	/**
	 * Retrieve newsletters for a given type
	 * @param  string $type
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getForType($type)
	{
		$this->guardType($type);

		return Newsletter::whereType($type)
			->with('user')
			->get();
	}

	/**
	 * Create a newsletter item for the given user
	 * @var TeenQuotes\Users\Models\User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
	 */
	public function createForUserAndType(User $user, $type)
	{
		$this->guardType($type);

		if ($this->userIsSubscribedToNewsletterType($user, $type)) return;

		$newsletter          = new Newsletter;
		$newsletter->type    = $type;
		$newsletter->user_id = $user->id;
		$newsletter->save();
	}

	/**
	 * Delete a newsletter item for the given user
	 * @var TeenQuotes\Users\Models\User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
	 */
	public function deleteForUserAndType(User $u, $type)
	{
		$this->guardType($type);

		return Newsletter::forUser($u)
			->type($type)
			->delete();
	}

	/**
	 * Delete newsletters for a list of users
	 * @param  array  $ids The ID of the users
	 * @return int The number of affected rows
	 */
	public function deleteForUsers(array $ids)
	{
		return Newsletter::whereIn('user_id', $ids)->delete();
	}

	private function guardType($type)
	{
		if ( ! in_array($type, [Newsletter::WEEKLY, Newsletter::DAILY]))
			throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type." was given.");
	}
}