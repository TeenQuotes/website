<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Newsletters;

use Illuminate\Support\Collection;
use TeenQuotes\Users\Models\User;

interface NewsletterList
{
    /**
     * Subscribe a user to a newsletter list.
     *
     * @param string $listName
     * @param User   $email
     *
     * @return mixed
     */
    public function subscribeTo($listName, User $user);

    /**
     * Subscribe multiple users to a newsletter.
     *
     * @param string     $listName
     * @param Collection $collection A collection of users
     *
     * @return mixed
     */
    public function subscribeUsersTo($listName, Collection $collection);

    /**
     * Unsubscribe a user from a newsletter list.
     *
     * @param string $listName
     * @param User   $email
     *
     * @return mixed
     */
    public function unsubscribeFrom($listName, User $user);

    /**
     * Unsubscribe multiple users from a newsletter.
     *
     * @param string     $listName
     * @param Collection $collection A collection of users
     *
     * @return mixed
     */
    public function unsubscribeUsersFrom($listName, Collection $collection);

    /**
     * Send a campaign to a list.
     *
     * @param string $listName
     * @param string $subject
     * @param string $toName
     * @param string $viewName
     * @param array  $viewData
     *
     * @return mixed
     */
    public function sendCampaign($listName, $subject, $toName, $viewName, $viewData);

    /**
     * Get users who unsubscribed from a list.
     *
     * @param string $listName
     *
     * @return Collection A collection of users
     */
    public function getUnsubscribesFromList($listName);

    /**
     * Get a mailing list ID from its user-friendly name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getListIdFromName($name);

    /**
     * Get the user-friendly name of a mailing list from its ID.
     *
     * @param string $listId
     *
     * @return string
     */
    public function getNameFromListId($listId);
}
