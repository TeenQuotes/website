<?php

use Illuminate\Support\Collection;
use TeenQuotes\Newsletters\Models\Newsletter;

class NewsletterRepoCest {

	/**
	 * @var TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	private $repo;

	public function _before()
	{
		$this->repo = App::make('TeenQuotes\Newsletters\Repositories\NewsletterRepository');
	}

	public function testUserIsSubscribedToNewsletterType(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(1, 'Newsletter', ['user_id' => $u->id, 'type' => Newsletter::WEEKLY]);

		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::DAILY));
		$I->assertTrue($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::WEEKLY));
	}

	public function testGetForType(IntegrationTester $I)
	{
		$I->assertEmpty($this->repo->getForType(Newsletter::WEEKLY));
		$I->assertEmpty($this->repo->getForType(Newsletter::DAILY));

		$I->insertInDatabase(3, 'Newsletter', ['type' => Newsletter::DAILY]);

		$newsletters = $this->repo->getForType(Newsletter::DAILY);

		$I->assertIsCollection($newsletters);
		$I->assertEquals(3, count($newsletters));
	}

	public function testCreateForUserAndType(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');

		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::DAILY));

		$this->repo->createForUserAndType($u, Newsletter::DAILY);

		$I->assertTrue($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::DAILY));
	}

	public function testDeleteForUserAndType(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');

		// A user is automatically subscribed to the weekly newsletter
		// when signing up
		$I->assertTrue($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::WEEKLY));

		$this->repo->deleteForUserAndType($u, Newsletter::WEEKLY);

		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::WEEKLY));
	}

	public function testDeleteForUser(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');

		// User will be subscribed to the daily and weekly newsletters after
		$this->repo->createForUserAndType($u, Newsletter::DAILY);

		$this->repo->deleteForUser($u);

		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::WEEKLY));
		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($u, Newsletter::DAILY));
	}

	public function testDeleteForUsers(IntegrationTester $I)
	{
		$usersArray = $I->insertInDatabase(2, 'User');

		// Subscribe them to the daily newsletter
		$this->repo->createForUserAndType($usersArray[0], Newsletter::DAILY);
		$this->repo->createForUserAndType($usersArray[1], Newsletter::DAILY);

		// Remove all newsletters for these users
		$this->repo->deleteForUsers(new Collection($usersArray));

		// Check that it has been done properly
		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($usersArray[0], Newsletter::DAILY));
		$I->assertFalse($this->repo->userIsSubscribedToNewsletterType($usersArray[1], Newsletter::DAILY));
	}
}