<?php

namespace TeenQuotes\Newsletters\Repositories;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Users\Models\User;

class DbNewsletterRepository implements NewsletterRepository
{
    /**
     * Tells if a user if subscribed to a newsletter type.
     *
     * @param User   $u    The given user
     * @param string $type The newsletter's type
     *
     * @return bool
     *
     * @see    \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
     */
    public function userIsSubscribedToNewsletterType(User $u, $type)
    {
        $this->guardType($type);

        return Newsletter::forUser($u)
            ->type($type)
            ->count() > 0;
    }

    /**
     * Retrieve newsletters for a given type.
     *
     * @param string $type
     *
     * @return Collection
     *
     * @see    \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
     */
    public function getForType($type)
    {
        $this->guardType($type);

        return Newsletter::whereType($type)
            ->with('user')
            ->get();
    }

    /**
     * Create a newsletter item for the given user.
     *
     * @var \TeenQuotes\Users\Models\User The user instance
     * @var string                        The type of the newsletter : weekly|daily
     *
     * @see \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
     */
    public function createForUserAndType(User $user, $type)
    {
        $this->guardType($type);

        if ($this->userIsSubscribedToNewsletterType($user, $type)) {
            return null;
        }

        $newsletter          = new Newsletter();
        $newsletter->type    = $type;
        $newsletter->user_id = $user->id;
        $newsletter->save();
    }

    /**
     * Delete a newsletter item for the given user.
     *
     * @var \TeenQuotes\Users\Models\User The user instance
     * @var string                        The type of the newsletter : weekly|daily
     *
     * @see \TeenQuotes\Newsletters\Models\Newsletter::getPossibleTypes()
     */
    public function deleteForUserAndType(User $u, $type)
    {
        $this->guardType($type);

        return Newsletter::forUser($u)
            ->type($type)
            ->delete();
    }

    /**
     * Delete all newsletters for a given user.
     *
     * @param User $u
     *
     * @return int The number of affected rows
     */
    public function deleteForUser(User $u)
    {
        return Newsletter::where('user_id', $u->id)->delete();
    }

    /**
     * Delete newsletters for a list of users.
     *
     * @param Collection $users The collection of users
     *
     * @return int The number of affected rows
     */
    public function deleteForUsers(Collection $users)
    {
        return Newsletter::whereIn('user_id', $users->lists('id'))->delete();
    }

    private function guardType($type)
    {
        $possibleTypes = Newsletter::getPossibleTypes();

        if (!in_array($type, $possibleTypes)) {
            throw new InvalidArgumentException($type.' was given. Possible types: '.implode('|', $possibleTypes));
        }
    }
}
