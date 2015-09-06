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

use Illuminate\Support\ServiceProvider;
use Mailchimp;
use TeenQuotes\Newsletters\Mailchimp\NewsletterList as MailchimpNewsletterList;

class NewsletterListServiceProvider extends ServiceProvider
{
    /**
     * Register binding in IoC container.
     */
    public function register()
    {
        $app = $this->app;

        $this->app->singleton('MailchimpClient', function () use ($app) {
            return new Mailchimp($app['config']->get('services.mailchimp.secret'));
        });

        $app->bind(
            NewsletterList::class,
            MailchimpNewsletterList::class
        );
    }
}
