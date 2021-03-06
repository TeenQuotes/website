<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Queues\Workers;

use Easyrec;
use Illuminate\Foundation\Application as App;
use URL;

class EasyrecWorker
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Register the view of a quote.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data Required keys: quote_id and user_id.
     */
    public function viewQuote($job, $data)
    {
        if (!$this->isEnabled()) {
            return null;
        }

        Easyrec::view($data['quote_id'],
            'Quote '.$data['quote_id'],
            URL::route('quotes.show', $data['quote_id'], false),
            $data['user_id'],
            null, // No image URL
            null, // Current timestamp
            'QUOTE'
        );
    }

    /**
     * Register the view of a user profile.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data Required keys: viewer_user_id, user_login and user_id.
     */
    public function viewUserProfile($job, $data)
    {
        if (!$this->isEnabled()) {
            return null;
        }

        Easyrec::view($data['user_id'],
            'User '.$data['user_id'],
            URL::route('users.show', $data['user_login'], false),
            $data['viewer_user_id'],
            null, // No image URL
            null, // Current timestamp
            'USERPROFILE'
        );
    }

    /**
     * Add a quote to the favorites.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data Required keys: quote_id, user_id
     */
    public function favoriteAQuote($job, $data)
    {
        if (!$this->isEnabled()) {
            return null;
        }

        Easyrec::sendAction($data['quote_id'],
            'Quote '.$data['quote_id'],
            URL::route('quotes.show', $data['quote_id'], false),
            'FAVORITE',
            null, // Action value
            $data['user_id'],
            null, // No image URL
            null, // Current timestamp
            'QUOTE'
        );
    }

    /**
     * Remove a quote from the favorites.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data Required keys: quote_id, user_id
     */
    public function unfavoriteAQuote($job, $data)
    {
        if (!$this->isEnabled()) {
            return null;
        }

        Easyrec::sendAction($data['quote_id'],
            'Quote '.$data['quote_id'],
            URL::route('quotes.show', $data['quote_id'], false),
            'UNFAVORITE',
            null, // Action value
            $data['user_id'],
            null, // No image URL
            null, // Current timestamp
            'QUOTE'
        );
    }

    private function isEnabled()
    {
        return $this->app->environment() == 'production';
    }
}
