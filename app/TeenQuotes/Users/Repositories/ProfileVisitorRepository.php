<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Repositories;

interface ProfileVisitorRepository
{
    /**
     * Tell that a user visited another user's profile.
     *
     * @param int|\TeenQuotes\Users\Models\User $visited
     * @param int|\TeenQuotes\Users\Models\User $visitor
     */
    public function addVisitor($visited, $visitor);

    /**
     * Get visitors for a given user.
     *
     * @param int|\TeenQuotes\Users\Models\User $u
     * @param int                               $page
     * @param int                               $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVisitors($u, $page, $pagesize);

    /**
     * Get visitors' information for a given user.
     *
     * @param int|\TeenQuotes\Users\Models\User $u
     * @param int                               $page
     * @param int                               $pagesize
     *
     * @return array ['login' => 'avatarURL'] array
     */
    public function getVisitorsInfos($u, $page, $pagesize);

    /**
     * Tells if a user has visited the profile of another user.
     *
     * @param int|\TeenQuotes\Users\Models\User $visitor
     * @param int|\TeenQuotes\Users\Models\User $visited
     *
     * @return bool
     */
    public function hasVisited($visitor, $visited);
}
