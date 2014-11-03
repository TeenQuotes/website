<?php namespace TeenQuotes\Tools\Validation;

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

	public function __call($name, $arguments)
	{
		if (Str::startsWith($name, 'validate'))
		{
			$ruleName = str_replace('validate', '', $name);
			
			if (count($arguments) == 1)
				return $this->validateForRule($arguments[0], 'rules'.$ruleName);
			
			return $this->validateForRule($arguments[0], 'rules'.$ruleName, $arguments[1]);
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