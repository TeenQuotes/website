<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laracasts\TestDummy\Factory as TestDummy;

class DbSeederHelper extends Module
{
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
		if ($this->isNiceBehaviourClass($class))
		{
			$plural = ucfirst(strtolower(Str::plural($class)));

			return 'TeenQuotes\\'.$plural.'\\Models\\'.ucfirst(strtolower($class));
		}

		// Other classes
		switch ($class)
		{
			case 'FavoriteQuote':
				return 'TeenQuotes\\Quotes\\Models\\FavoriteQuote';
		}

		// We haven't be able to resolve this class
		throw new InvalidArgumentException("Can't resolve the full namespace for the given class name: ".$class);
	}

	private function isNiceBehaviourClass($name)
	{
		$niceClasses = [
			'comment',
			'country',
			'newsletter',
			'quote',
			'setting',
			'story',
			'tag',
			'user',
		];

		return in_array(strtolower($name), $niceClasses);
	}
}