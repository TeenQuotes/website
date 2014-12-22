<?php namespace Codeception\Module;

use Codeception\Module;

class IntegrationHelper extends Module {

	public function assertIsCollection($object)
	{
		$I = $this->getModule('Asserts');

		$I->assertTrue($object instanceof \Illuminate\Database\Eloquent\Collection);
	}
}