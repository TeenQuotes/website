<?php

use Codeception\TestCase\Test;

abstract class ApiTest extends Test {

	/**
	* @var UnitTester
	*/
	protected $tester;
	
	protected function _before()
	{
		Artisan::call('migrate');

		// We'll run all tests through a transaction,
		// and then rollback afterward.
		DB::beginTransaction();

		// Set required attributes
		$this->unitTester->setRequiredAttributes($this->requiredAttributes);

		$this->unitTester->setEmbedsRelation([]);
	}

	protected function _after()
	{
		if (Auth::check())
			Auth::logout();
		
		// Rollback the transaction
		DB::rollBack();
	}
}