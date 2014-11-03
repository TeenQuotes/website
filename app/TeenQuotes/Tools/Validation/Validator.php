<?php namespace TeenQuotes\Tools\Validation;

use BadMethodCallException;
use Laracasts\Validation\FormValidationException;
use Laracasts\Validation\LaravelValidator;
use Str;

abstract class Validator extends LaravelValidator {

	/**
	 * Validate data against a set of rules
	 * @param  array $data The key-value data
	 * @param  string $rule The name of the property for the rules
	 * @param  array $messages
	 * @return boolean
	 * @throws Laracasts\Validation\FormValidationException
	 */
	protected function validateForRule($data, $rule, $messages = [])
	{
		$this->validation = $this->make($data, $this->$rule, $messages);

		return $this->handleValidation();
	}

	/**
	 * Magic call method to forward validate* methods
	 * @param  string $name
	 * @param  array $arguments
	 * @return mixed
	 * @throws BadMethodCallException
	 */
	public function __call($name, $arguments)
	{
		if (Str::startsWith($name, 'validate'))
		{
			$property = 'rules'.str_replace('validate', '', $name);

			if (! property_exists($this, $property))
				throw new BadMethodCallException("Property ".$property." does not exist on class ".get_class($this).".");
			
			if (count($arguments) == 1)
				return $this->validateForRule($arguments[0], $property);
			
			return $this->validateForRule($arguments[0], $property, $arguments[1]);
		}
	}

	private function handleValidation()
	{
		if ($this->validation->fails())
		{
			throw new FormValidationException(
				$this->validation->errors()->first($this->getFirstKeyFail()),
				$this->validation->errors()
			);
		}

		return true;
	}

	private function getFirstKeyFail()
	{
		$keys = array_keys($this->validation->errors()->getMessages());

		return $keys[0];
	}
}