<?php namespace TeenQuotes\Users\Repositories;

interface UserRepository {

	/**
	 * Retrieve a user by its ID
	 * @param int $id
	 * @return TeenQuotes\Users\Models\User
	 */
	public function getById($id);

	/**
	 * Retrieve a user by its email address
	 * @param  string $email
	 * @return TeenQuotes\Users\Models\User
	 */
	public function getByEmail($email);

	/**
	 * Get users from an array of emails
	 * @param array  $emails Email addresses
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getByEmails(array $emails);

	/**
	 * Retrieve a user by its login
	 * @param string $login
	 * @return TeenQuotes\Users\Models\User
	 */
	public function getByLogin($login);

	/**
	 * Count the number of users that match the given login. Do not count users with an hidden profile.
	 * @param string $login
	 * @return int
	 */
	public function countByPartialLogin($login);

	/**
	 * Update the password for a user
	 * @param TeenQuotes\Users\Models\User|int $u
	 * @param string $password
	 */
	public function updatePassword($u, $password);

	/**
	 * Update a user's profile
	 * @param TeenQuotes\Users\Models\User|int $u
	 * @param string $gender
	 * @param int $country
	 * @param string $city
	 * @param string $about_me
	 * @param string $birthdate
	 * @param Symfony\Component\HttpFoundation\File\UploadedFile $avatar
	 */
	public function updateProfile($u, $gender, $country, $city, $about_me, $birthdate, $avatar);

	/**
	 * Update a user's settings
	 * @param TeenQuotes\Users\Models\User|int $u
	 * @param boolean $notification_comment_quote
	 * @param boolean $hide_profile
	 */
	public function updateSettings($u, $notification_comment_quote, $hide_profile);

	/**
	 * Get all users
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getAll();

	/**
	 * Retrieve users who have their birthday today
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function birthdayToday();

	/**
	 * Retrieve a user by its login or its ID
	 * @param string|int $user_id
	 * @return TeenQuotes\Users\Models\User
	 */
	public function showByLoginOrId($user_id);

	/**
	 * Get users that have logged in since a given date
	 * @param DateTime $since
	 * @param int $page
	 * @param int $pagesize
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getLoggedInSince($since, $page, $pagesize);

	/**
	 * Search user matching a login
	 * @param string $query
	 * @param int $page
	 * @param int $pagesize
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function searchByPartialLogin($query, $page, $pagesize);

	/**
	 * Create a user
	 * @param string $login
	 * @param string $email
	 * @param string $password
	 * @param string $ip
	 * @param string $lastVisit
	 * @param int $country
	 * @param string $city
	 * @param string $avatar
	 * @return TeenQuotes\Users\Models\User
	 */
	public function create($login, $email, $password, $ip, $lastVisit, $country, $city, $avatar = null);

	/**
	 * Delete a user
	 * @param int|TeenQuotes\Users\Models\User $id
	 * @return TeenQuotes\Users\Models\User
	 */
	public function destroy($id);

	/**
	 * Get the most common country ID
	 * @return int
	 */
	public function mostCommonCountryId();

	/**
	 * Retrieve users who have not logged in in the last year and who are subscribed to at least a newsletter
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getNonActiveHavingNewsletter();
}