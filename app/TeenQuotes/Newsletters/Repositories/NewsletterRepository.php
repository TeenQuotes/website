<?php namespace TeenQuotes\Newsletters\Repositories;

use TeenQuotes\Users\Models\User;

interface NewsletterRepository {
	
	/**
	 * Tells if a user if subscribed to a newsletter type
	 * @param  TeenQuotes\Users\Models\User   $u    The given user
	 * @param  string $type The newsletter's type
	 * @return bool
	 */
	public function userIsSubscribedToNewsletterType(User $u, $type);

	/**
	 * Retrieve newsletters for a given type
	 * @param  string $type
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getForType($type);
	
	/**
	 * Create a newsletter item for the given user
	 * @var TeenQuotes\Users\Models\User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
	 * @throws InvalidArgumentException If the newsletter's type is wrong or if
	 * the user is already subscribed to the newsletter.
	 */
	public function createForUserAndType(User $user, $type);

	/**
	 * Delete a newsletter item for the given user
	 * @var TeenQuotes\Users\Models\User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
	 */
	public function deleteForUserAndType(User $u, $type);
}