<?php

class ProfileVisitorRepoCest {

	/**
	 * @var \TeenQuotes\Users\Repositories\ProfileVisitorRepository
	 */
	private $repo;

	public function _before()
	{
		$this->repo = App::make('TeenQuotes\Users\Repositories\ProfileVisitorRepository');
	}

	public function testAddVisitor(IntegrationTester $I)
	{
		$I->insertInDatabase(2, 'User');

		$this->repo->addVisitor(1, 2);

		$I->assertTrue($this->repo->hasVisited(2, 1));
		$I->assertFalse($this->repo->hasVisited(1, 2));

		$this->repo->addVisitor(2, 1);
		$I->assertTrue($this->repo->hasVisited(1, 2));
	}

	public function testGetVisitors(IntegrationTester $I)
	{
		$I->insertInDatabase(2, 'User');
		$I->insertInDatabase(1, 'User', ['hide_profile' => 1]);
		$I->insertInDatabase(1, 'User');

		$I->assertEmpty($this->repo->getVisitors(1, 1, 10));

		$this->repo->addVisitor(1, 2);
		$I->assertEquals(1, count($this->repo->getVisitors(1, 1, 10)));

		// A visitor with an hidden profile visits user #1
		$this->repo->addVisitor(1, 3);
		$I->assertEquals(1, count($this->repo->getVisitors(1, 1, 10)));

		// It gets the last visitor first
		$this->repo->addVisitor(1, 4);
		$I->assertEquals(2, count($this->repo->getVisitors(1, 1, 10)));
		$I->assertEquals(4, $this->repo->getVisitors(1, 1, 10)->first()->id);

		// We don't have duplicates
		$this->repo->addVisitor(1, 2);
		$visitorsIds = $this->repo->getVisitors(1, 1, 10)->lists('id');
		sort($visitorsIds);
		$I->assertEquals([2, 4], $visitorsIds);
	}

	public function testGetVisitorsInfo(IntegrationTester $I)
	{
		$urlAvatar = 'http://example.com/image.jpg';

		$I->insertInDatabase(1, 'User');
		$I->insertInDatabase(1, 'User', ['login' => 'foo', 'avatar' => $urlAvatar]);
		$I->insertInDatabase(1, 'User', ['hide_profile' => 1]);
		$I->insertInDatabase(1, 'User', ['login' => 'test', 'avatar' => $urlAvatar]);

		$this->repo->addVisitor(1, 2);
		$expected = ['foo' => $urlAvatar];
		$I->assertEquals($expected, $this->repo->getVisitorsInfos(1, 1, 10));

		// A visitor with an hidden profile
		$this->repo->addVisitor(1, 3);
		$I->assertEquals($expected, $this->repo->getVisitorsInfos(1, 1, 10));

		// Add another valid visitor
		$this->repo->addVisitor(1, 4);
		$expected['test'] = $urlAvatar;
		$I->assertEquals($expected, $this->repo->getVisitorsInfos(1, 1, 10));
	}
}