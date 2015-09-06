<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Newsletters\Controllers;

use BaseController;
use Config;
use Input;
use InvalidArgumentException;
use Response;
use TeenQuotes\Newsletters\NewsletterList;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Users\Repositories\UserRepository;

class MailchimpWebhook extends BaseController
{
    /**
     * @var NewsletterRepository
     */
    private $newsletterRepo;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var NewsletterList
     */
    private $newsletterList;

    /**
     * @param UserRepository       $userRepo
     * @param NewsletterRepository $newsletterRepo
     * @param NewsletterList       $newsletterList
     */
    public function __construct(UserRepository $userRepo, NewsletterRepository $newsletterRepo,
                                NewsletterList $newsletterList)
    {
        $this->userRepo       = $userRepo;
        $this->newsletterRepo = $newsletterRepo;
        $this->newsletterList = $newsletterList;
    }

    /**
     * Listen for the incoming webhooks and handle them.
     *
     * @return Response
     */
    public function listen()
    {
        $this->checkKey(Input::get('key'));

        $type = Input::get('type');

        switch ($type) {
            // Unsubscribe from Mailchimp website
            case 'unsubscribe':
                $this->unsubscribe(Input::get('data'));
                break;

            // Update of the email address
            case 'upemail':
                $this->changeEmail(Input::get('data'));
                break;

            // Hard bounce or spam complaint
            case 'cleaned':
                $this->bounce(Input::get('data'));
                break;
        }

        return Response::make('DONE', 200);
    }

    /**
     * Handle the bounce event.
     *
     * @param array $data
     */
    private function bounce(array $data)
    {
        $user = $this->userRepo->getByEmail($data['email']);

        if (!is_null($user)) {
            $this->newsletterRepo->deleteForUser($user);
        }
    }

    /**
     * Handle the unsubscribe event.
     *
     * @param array $data
     */
    private function unsubscribe(array $data)
    {
        $type = $this->getTypeFromListId($data['list_id']);

        $user = $this->userRepo->getByLogin($data['merges']['LOGIN']);

        if (!is_null($user)) {
            $this->newsletterRepo->deleteForUserAndType($user, $type);
        }
    }

    /**
     * Handle the event when an user has changed its email address.
     *
     * @param array $data
     */
    private function changeEmail(array $data)
    {
        $oldEmail = $data['old_email'];
        $newEmail = $data['new_email'];

        $user = $this->userRepo->getByEmail($oldEmail);

        if (!is_null($user)) {
            $this->userRepo->updateEmail($user, $newEmail);
        }
    }

    /**
     * Get the type of a newsletter from its list ID.
     *
     * @param string $listId
     *
     * @return string
     */
    private function getTypeFromListId($listId)
    {
        return str_replace('Newsletter', '', $this->newsletterList->getNameFromListId($listId));
    }

    /**
     * Check the given secret key.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException The key was wrong
     */
    private function checkKey($key)
    {
        if ($key != Config::get('services.mailchimp.secret')) {
            throw new InvalidArgumentException('The secret key is not valid. Got '.$key);
        }
    }
}
