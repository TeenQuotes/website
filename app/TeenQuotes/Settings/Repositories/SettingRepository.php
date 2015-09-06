<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Settings\Repositories;

use TeenQuotes\Users\Models\User;

interface SettingRepository
{
    /**
     * Update or create a setting for a given user and key.
     *
     * @param \TeenQuotes\Users\Models\User $u
     * @param string                        $key
     * @param mixed                         $value
     *
     * @return \TeenQuotes\Settings\Models\Setting
     */
    public function updateOrCreate(User $u, $key, $value);

    /**
     * Retrieve a row for a given user and key.
     *
     * @param \TeenQuotes\Users\Models\User $u
     * @param string                        $key
     *
     * @return \TeenQuotes\Settings\Models\Setting
     */
    public function findForUserAndKey(User $u, $key);
}
