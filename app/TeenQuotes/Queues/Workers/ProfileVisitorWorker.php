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

use TeenQuotes\Users\Repositories\ProfileVisitorRepository;

class ProfileVisitorWorker
{
    /**
     * @var \TeenQuotes\Users\Repositories\ProfileVisitorRepository
     */
    private $repo;

    public function __construct(ProfileVisitorRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * View a user profile.
     *
     * @param \Illuminate\Queue\Jobs\SyncJob $job
     * @param array                          $data Required keys: visitor_id and user_id.
     */
    public function viewProfile($job, $data)
    {
        $this->repo->addVisitor($data['user_id'], $data['visitor_id']);
    }
}
