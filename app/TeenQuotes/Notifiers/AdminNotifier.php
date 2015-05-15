<?php

namespace TeenQuotes\Notifiers;

interface AdminNotifier
{
    /**
     * Notify an administrator about an event.
     *
     * @param string $message
     */
    public function notify($message);
}
