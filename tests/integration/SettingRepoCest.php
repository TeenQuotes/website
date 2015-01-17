<?php

class SettingRepoCest {

	/**
	 * @var TeenQuotes\Settings\Repositories\SettingRepository
	 */
	private $repo;

	public function _before()
	{
		$this->repo = App::make('TeenQuotes\Settings\Repositories\SettingRepository');
	}

	public function testFindForUserAndKey(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$this->insertSetting($I, $u->id, 'foo', 'bar');

		$s = $this->repo->findForUserAndKey($u, 'foo');

		$I->assertEquals('bar', $s->value);

		// Unexisting key
		$I->assertNull($this->repo->findForUserAndKey($u, 'notfound'));
	}

	public function testUpdateOrCreate(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$this->insertSetting($I, $u->id, 'foo', 'bar');

		// Update a value
		$this->repo->updateOrCreate($u, 'foo', 'new');

		$s = $this->repo->findForUserAndKey($u, 'foo');

		$I->assertEquals('new', $s->value);
		$I->assertEquals('foo', $s->key);
		$I->assertEquals($u->id, $s->user_id);

		// Create a new value
		$this->repo->updateOrCreate($u, 'new', 'foo');

		$s = $this->repo->findForUserAndKey($u, 'new');

		$I->assertEquals('foo', $s->value);
		$I->assertEquals('new', $s->key);
		$I->assertEquals($u->id, $s->user_id);
	}

	private function insertSetting(IntegrationTester $I, $user_id, $key, $value)
	{
		$data = compact('user_id', 'key', 'value');

		return $I->insertInDatabase(1, 'Setting', $data);
	}
}