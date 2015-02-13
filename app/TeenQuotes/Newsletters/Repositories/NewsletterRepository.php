<?php namespace TeenQuotes\Newsletters\Repositories;

use Illuminate\Support\Collection;
use TeenQuotes\Users\Models\User;

interface NewsletterRepository {

	/**
	 * Tells if a user if subscribed to a newsletter type
	 * @param  \TeenQuotes\Users\Models\User $u    The given user
	 * @param  string $type The newsletter's type
	 * @return bool
	 * @see \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
	 */
	public function userIsSubscribedToNewsletterType(User $u, $type);

	/**
	 * Retrieve newsletters for a given type
	 * @param  string $type
	 * @return \Illuminate\Database\Eloquent\Collection
	 * @see \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
	 */
	public function getForType($type);

	/**
	 * Create a newsletter item for the given user
	 * @var \TeenQuotes\Users\Models\User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
	 * @see \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
	 */
	public function createForUserAndType(User $user, $type);

	/**
	 * Delete a newsletter item for the given user
	 * @var \TeenQuotes\Users\Models\User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
	 * @see \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
	 */
	public function deleteForUserAndType(User $u, $type);

	/**
	 * Delete all newsletters for a given user
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @return int The number of affected rows
	 */
	public function deleteForUser(User $u);

	/**
	 * Delete newsletters for a list of users
	 * @param  \Illuminate\Support\Collection $users The collection of users
	 * @return int The number of affected rows
	 */
	public function deleteForUsers(Collection $users);
}