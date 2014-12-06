<?php namespace TeenQuotes\Tools\Namespaces;

use ReflectionClass;

trait NamespaceTrait {

	public function getBaseNamespace()
	{	
		$reflection = new ReflectionClass(__CLASS__);
		return $reflection->getNamespaceName().'\\';
	}

	public function getNamespaceComposers()
	{
		return $this->getBaseNamespace().'Composers\\';
	}

	public function getNamespaceConsole()
	{
		return $this->getBaseNamespace().'Console\\';
	}
}