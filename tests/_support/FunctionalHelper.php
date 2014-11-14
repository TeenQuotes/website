<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laracasts\TestDummy\Factory as TestDummy;
use TeenQuotes\Quotes\Models\Quote;
use InvalidArgumentException;

class FunctionalHelper extends Module
{
	/**
	 * Assert that I can see an error message on a form
	 * @param  string $message The expected message
	 */
	public function seeFormError($message)
	{
		$I = $this->getModule('Laravel4');
		
		$I->see($message, '.error-form');
	}

	/**
	 * Assert that we can see a success alert with a given message
	 * @param  string $message The expected message
	 */
	public function seeSuccessFlashMessage($message)
	{
		$I = $this->getModule('Laravel4');
		
		$I->see($message, '.alert-success');
	}

	/**
	 * Create a new user and store it in database. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return TeenQuotes\Users\Models\User The created user instance
	 */
	public function haveAnAccount($overrides = [])
	{		
		return $this->insertInDatabase(1, 'User', $overrides);
	}

	/**
	 * Create a new user. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return TeenQuotes\Users\Models\User The created user instance
	 */
	public function buildUser($overrides = [])
	{
		return TestDummy::build($this->classToFullNamespace('User'), $overrides);		
	}

	/**
	 * Log a new user. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return User The logged in user
	 */
	public function logANewUser($overrides = [])
	{
		$u = $this->haveAnAccount($overrides);

		Auth::loginUsingId($u->id);

		return $u;
	}

	public function sendAjaxDeleteRequest($uri, $params = [])
	{
		$this->getModule('Laravel4')->sendAjaxRequest('DELETE', $uri, $params);
	}

	public function sendAjaxPutRequest($uri, $params = [])
	{
		$this->getModule('Laravel4')->sendAjaxRequest('PUT', $uri, $params);
	}

	/**
	 * Insert a record in database
	 * @param  int $times The number of elements to insert
	 * @param  string $class The name of the class to insert
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return array|object The created record(s)
	 */
	public function insertInDatabase($times, $class, $overrides = [])
	{
		return TestDummy::times($times)->create($this->classToFullNamespace($class), $overrides);
	}

	/**
	 * Resolve a class name to its full namespace
	 * @param  string $class
	 * @return string
	 */
	private function classToFullNamespace($class)
	{
		// "Nice behaviour" classes
		if (in_array(strtolower($class), ['comment', 'country', 'newsletter', 'quote', 'user', 'story'])) {
			$plural = ucfirst(strtolower(Str::plural($class)));
			
			return 'TeenQuotes\\'.$plural.'\\Models\\'.ucfirst(strtolower($class));
		}

		// Other classes
		switch ($class) {
			case 'FavoriteQuote':
				return 'TeenQuotes\\Quotes\\Models\\FavoriteQuote';
		}

		// We haven't be able to resolve this class
		throw new InvalidArgumentException("Can't resolve the full namespace for the given class name: ".$class);
	}
}