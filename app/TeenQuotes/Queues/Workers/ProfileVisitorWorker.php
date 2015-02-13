<?php namespace TeenQuotes\Queues\Workers;

use TeenQuotes\Users\Repositories\ProfileVisitorRepository;

class ProfileVisitorWorker {

	/**
	 * @var \TeenQuotes\Users\Repositories\ProfileVisitorRepository
	 */
	private $repo;

	public function __construct(ProfileVisitorRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * View a user profile
	 *
	 * @param  \Illuminate\Queue\Jobs\SyncJob $job
	 * @param  array $data Required keys: visitor_id and user_id.
	 */
	public function viewProfile($job, $data)
	{
		$this->repo->addVisitor($data['user_id'], $data['visitor_id']);
	}
}