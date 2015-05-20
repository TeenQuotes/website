<?php

namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laracasts\TestDummy\Factory as TestDummy;
use TeenQuotes\Quotes\Models\Quote;

class FunctionalHelper extends Module
{
    /**
     * Assert that I can see an error message on a form.
     *
     * @param string $message The expected message
     */
    public function seeFormError($message)
    {
        $I = $this->getModule('Laravel4');

        $I->see($message, '.error-form');
    }

    /**
     * Assert that we can see a success alert with a given message.
     *
     * @param string $message The expected message
     */
    public function seeSuccessFlashMessage($message)
    {
        $I = $this->getModule('Laravel4');

        $I->see($message, '.alert-success');
    }

    /**
     * Create a new user. Can pass an array (key-value) to override dummy values.
     *
     * @param array $overrides The key-value array used to override dummy values
     *
     * @return TeenQuotes\Users\Models\User The created user instance
     */
    public function buildUser($overrides = [])
    {
        return TestDummy::build($this->classToFullNamespace('User'), $overrides);
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
     * Resolve a class name to its full namespace.
     *
     * @param string $class
     *
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
