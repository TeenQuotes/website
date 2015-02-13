<?php namespace TeenQuotes\Tools\Namespaces;

use Str, ReflectionClass;

trait NamespaceTrait {

	public function getBaseNamespace()
	{
		$reflection = new ReflectionClass(__CLASS__);
		return $reflection->getNamespaceName().'\\';
	}

	public function __call($name, $arguments)
	{
		// Handle getNamespace with a directory name
		if (Str::startsWith($name, 'getNamespace'))
		{
			$directory = str_replace('getNamespace', '', $name);

			return $this->getBaseNamespace().$directory.'\\';
		}

		// Return other calls
		return call_user_func_array(
			array($this, $name),
			$arguments
		);
	}
}