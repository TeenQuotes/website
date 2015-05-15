<?php

namespace TeenQuotes\Users\Presenters;

use Carbon;
use Config;
use Laracasts\Presenter\Presenter;
use Request;
use Str;

class UserPresenter extends Presenter
{
    /**
     * Get the URL of the user's avatar.
     *
     * @return string The URL to the avatar
     */
    public function avatarLink()
    {
        // Full URL
        if (Str::startsWith($this->avatar, 'http')) {
            return $this->avatar;
        } elseif (is_null($this->avatar)) {
            return Config::get('app.users.avatar.default');
        }
        // Local URL
        else {
            return str_replace('public/', '', Request::root().'/'.Config::get('app.users.avatarPath').'/'.$this->avatar);
        }
    }

    /**
     * Get the name of the icon to display based on the gender of the user.
     *
     * @return string The name of the icon to display : fa-male|fa-female
     */
    public function iconGender()
    {
        return $this->entity->isMale() ? 'fa-male' : 'fa-female';
    }

    /**
     * The age of the user.
     *
     * @return int
     */
    public function age()
    {
        $carbon = Carbon::createFromFormat('Y-m-d', $this->birthdate);

        return $carbon->age;
    }
}
