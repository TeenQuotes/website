<?php namespace TeenQuotes\Users\Repositories;

use Carbon, DB, InvalidArgumentException;
use TeenQuotes\Countries\Models\Country;
use TeenQuotes\Users\Models\User;

class DbUserRepository implements UserRepository {

	/**
	 * Retrieve a user by its ID
	 * @param  int $id
	 * @return \TeenQuotes\Users\Models\User
	 */
	public function getById($id)
	{
		return User::find($id);
	}

	/**
	 * Retrieve a user by its email address
	 * @param  string $email
	 * @return \TeenQuotes\Users\Models\User
	 */
	public function getByEmail($email)
	{
		return User::whereEmail($email)->first();
	}

	/**
	 * Get users from an array of emails
	 * @param  array  $emails Email addresses
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getByEmails(array $emails)
	{
		return User::whereIn('email', $emails)->get();
	}

	/**
	 * Retrieve a user by its login
	 * @param  string $login
	 * @return \TeenQuotes\Users\Models\User
	 */
	public function getByLogin($login)
	{
		return User::whereLogin($login)->first();
	}

	/**
	 * Count the number of users that match the given login. Do not count users with an hidden profile.
	 * @param  string $login
	 * @return int
	 */
	public function countByPartialLogin($login)
	{
		return User::partialLogin($login)
			->notHidden()
			->count();
	}

	/**
	 * Update the password for a user
	 * @param  \TeenQuotes\Users\Models\User|int $u
	 * @param  string $password
	 */
	public function updatePassword($u, $password)
	{
		$user = $this->retrieveUser($u);
		$user->password = $password;
		$user->save();
	}

	/**
	 * Update the email for a user
	 * @param  \TeenQuotes\Users\Models\User|int $u
	 * @param  string $email
	 */
	public function updateEmail($u, $email)
	{
		$user = $this->retrieveUser($u);
		$user->email = $email;
		$user->save();
	}

	/**
	 * Update a user's profile
	 * @param  \TeenQuotes\Users\Models\User|int $u
	 * @param  string $gender
	 * @param  int $country
	 * @param  string $city
	 * @param  string $about_me
	 * @param  string $birthdate
	 * @param Symfony\Component\HttpFoundation\File\UploadedFile $avatar
	 */
	public function updateProfile($u, $gender, $country, $city, $about_me, $birthdate, $avatar)
	{
		$user = $this->retrieveUser($u);

		if ( ! empty($gender))
			$user->gender    = $gender;
		if ( ! empty($country))
			$user->country   = $country;
		if ( ! empty($city))
			$user->city      = $city;
		if ( ! empty($about_me))
			$user->about_me  = $about_me;
		$user->birthdate = empty($birthdate) ? null : $birthdate;

		if ( ! is_null($avatar)) {
			$filename = $user->id.'.'.$avatar->getClientOriginalExtension();
			$user->avatar = $filename;
		}

		$user->save();
	}

	/**
	 * Update a user's settings
	 * @param  \TeenQuotes\Users\Models\User|int $u
	 * @param boolean $notification_comment_quote
	 * @param boolean $hide_profile
	 */
	public function updateSettings($u, $notification_comment_quote, $hide_profile)
	{
		$user = $this->retrieveUser($u);
		$user->notification_comment_quote = $notification_comment_quote;
		$user->hide_profile               = $hide_profile;
		$user->save();
	}

	/**
	 * Get all users
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getAll()
	{
		return User::all();
	}

	/**
	 * Retrieve users who have their birthday today
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function birthdayToday()
	{
		return User::birthdayToday()
			->get();
	}

	/**
	 * Retrieve a user by its login or its ID
	 * @param  string|int $user_id
	 * @return \TeenQuotes\Users\Models\User
	 */
	public function showByLoginOrId($user_id)
	{
		return User::where('login', '=', $user_id)
			->orWhere('id', '=', $user_id)
			->with(array('countryObject' => function($q) {
				$q->addSelect(array('id', 'name'));
			}))
			->with(array('newsletters' => function($q) {
				$q->addSelect('user_id', 'type', 'created_at');
			}))
			->first();
	}

	/**
	 * Get users that have logged in since a given date
	 * @param DateTime $since
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getLoggedInSince($since, $page, $pagesize)
	{
		return User::where('last_visit', '>=', $since)
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Search user matching a login
	 * @param  string $query
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function searchByPartialLogin($query, $page, $pagesize)
	{
		return User::partialLogin($query)
			->notHidden()
			->with('countryObject')
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Create a user
	 * @param  string $login
	 * @param  string $email
	 * @param  string $password
	 * @param  string $ip
	 * @param  string $lastVisit
	 * @param  int $country
	 * @param  string $city
	 * @param  string $avatar
	 * @return \TeenQuotes\Users\Models\User
	 */
	public function create($login, $email, $password, $ip, $lastVisit, $country, $city, $avatar = null)
	{
		$user             = new User;
		$user->login      = $login;
		$user->email      = $email;
		$user->password   = $password;
		$user->ip         = $ip;
		$user->last_visit = $lastVisit;
		$user->country    = $country;
		$user->city       = $city;

		if (! is_null($avatar))
			$user->avatar = $avatar;

		$user->save();

		return $user;
	}

	/**
	 * Delete a user
	 * @param  int|\TeenQuotes\Users\Models\User $id
	 * @return \TeenQuotes\Users\Models\User
	 */
	public function destroy($u)
	{
		$u = $this->retrieveUser($u);
		$u->delete();

		return $u;
	}

	/**
	 * Get the most common country ID
	 * @return int
	 */
	public function mostCommonCountryId()
	{
		$u = User::select('country', DB::raw('count(*) as total'))
			->groupBy('country')
			->orderBy('total', 'DESC')
			->first();

		return $u->country;
	}

	/**
	 * Retrieve users who have not logged in in the last year and who are subscribed to at least a newsletter
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getNonActiveHavingNewsletter()
	{
		return User::where('last_visit', '<=', Carbon::now()->subYear())
			->has('newsletters')
			->get();
	}

	/**
	 * Get users from a country without an hidden profile
	 * @param  \TeenQuotes\Countries\Models\Country $c
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function fromCountry(Country $c, $page, $pagesize)
	{
		return User::notHidden()
			->fromCountry($c)
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	private function computeSkip($page, $pagesize)
	{
		return $pagesize * ($page - 1);
	}

	/**
	 * Retrieve a user by its ID or just the user instance
	 * @param  \TeenQuotes\Users\Models\User|int $u
	 * @return \TeenQuotes\Users\Models\User
	 * @throws \InvalidArgumentException If the type can't be recognised
	 */
	private function retrieveUser($u)
	{
		if (is_numeric($u))
			return $this->getById($u);

		if ($u instanceof User)
			return $u;

		throw new InvalidArgumentException("Expecting a user instance or a user ID.");
	}
}