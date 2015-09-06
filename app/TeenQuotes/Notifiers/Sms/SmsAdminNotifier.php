<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Notifiers\Sms;

use TeenQuotes\Notifiers\AdminNotifier;

class SmsAdminNotifier implements AdminNotifier
{
    /**
     * The base URL.
     *
     * @var string
     */
    private $url;

    /**
     * The username.
     *
     * @var string
     */
    private $user;

    /**
     * The password to use.
     *
     * @var string
     */
    private $password;

    public function __construct($url, $user, $password)
    {
        $this->url      = $url;
        $this->user     = $user;
        $this->password = $password;
    }

    /**
     * Notify an administrator about an event.
     *
     * @param string $message
     */
    public function notify($message)
    {
        $data = $this->constructData($message);

        $this->sendRequest($this->url, $data);
    }

    private function sendRequest($url, $data)
    {
        $ch   = curl_init();
        $full = $url.'?'.http_build_query($data, '', '&', PHP_QUERY_RFC3986);

        curl_setopt($ch, CURLOPT_URL, $full);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);
    }

    private function constructData($message)
    {
        return [
            'user' => $this->user,
            'pass' => $this->password,
            'msg'  => $message,
        ];
    }
}
