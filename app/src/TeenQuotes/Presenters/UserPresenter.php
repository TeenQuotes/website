<?php namespace TeenQuotes\Presenters;

use Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter {
	
	/**
	 * Get the URL of the user's avatar
	 * @return string The URL to the avatar
	 */
	public function avatarLink()
	{
		// Full URL
		if (Str::startsWith($this->avatar, 'http'))
			return $this->avatar;
		elseif (is_null($this->avatar))
			return Config::get('app.url').'/assets/images/chat.png';
		// Local URL
		else
			return str_replace('public/', '', Request::root().'/'.Config::get('app.users.avatarPath').'/'.$this->avatar);
	}

	/**
	 * Get the name of the icon to display based on the gender of the user
	 * @return string The name of the icon to display : fa-male|fa-female
	 */
	public function iconGender()
	{
		return $this->entity->isMale() ? 'fa-male' : 'fa-female';
	}

	/**
	 * The age of the user
	 * @return int
	 */
	public function age()
	{
		$carbon = Carbon::createFromFormat('Y-m-d', $this->birthdate);
		return $carbon->age;
	}
}