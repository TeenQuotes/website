<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'ip');

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rules = [
		'login' => 'required|unique:users,login|min:3|max:20',
		'password' => 'required|min:6',
		'email' => 'required|email',
		'ip' => 'ip',
		'birthdate' => 'date_format:"m-d-Y"',
		'gender' => 'in:M,W',
		'country' => 'alpha',
		'city' => 'alpha',
		'avatar' => 'image',
		'about_me' => 'max:500',
		'hide_profile' => 'in:0,1',
		'notification_comment_quote' => 'in:0,1',
	]; 

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function newsletters()
	{
		return $this->hasMany('Newsletter');
	}

	public function quotes()
	{
		return $this->hasMany('Quote');
	}

	public function stories()
	{
		return $this->hasMany('Story');
	}

	public function usersVisitors()
	{
		return $this->hasMany('ProfileVisitor', 'user_id', 'id');
	}

	public function usersVisited()
	{
		return $this->hasMany('ProfileVisitor', 'visitor_id', 'id');
	}

	public function favoriteQuotes()
    {
        return $this->belongsToMany('Quote', 'favorite_quotes');
    }

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

}
