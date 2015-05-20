<?php

namespace TeenQuotes\Mail;

use Illuminate\Support\Collection;
use Mandrill as M;
use TeenQuotes\Users\Repositories\UserRepository;

class Mandrill
{
    /**
     * The client for the Mandrill API.
     *
     * @var M
     */
    private $api;

    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(M $api, UserRepository $userRepo)
    {
        $this->api      = $api;
        $this->userRepo = $userRepo;
    }

    /**
     * Get email addresses that have already have an hard bounce.
     *
     * @return array
     */
    public function getHardBouncedEmails()
    {
        $result = $this->api->rejects->getList('', false);
        $collection = new Collection($result);

        $hardBounced = $collection->filter(function ($a) {
            return $a['reason'] == 'hard-bounce';
        });

        return $hardBounced->lists('email');
    }

    /**
     * Get users that have already been affected by an hard bounce.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHardBouncedUsers()
    {
        return $this->userRepo->getByEmails(
            $this->getHardBouncedEmails()
        );
    }

    /**
     * Delete an email address from the rejection list.
     *
     * @param string $email
     *
     * @return bool Whether the address was deleted successfully
     */
    public function deleteEmailFromRejection($email)
    {
        $result = $this->api->rejects->delete($email);

        return $result['deleted'];
    }
}
