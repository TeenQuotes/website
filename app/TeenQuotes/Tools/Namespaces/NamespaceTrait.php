<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tools\Namespaces;

use ReflectionClass;
use Str;

trait NamespaceTrait
{
    public function getBaseNamespace()
    {
        $reflection = new ReflectionClass(__CLASS__);

        return $reflection->getNamespaceName().'\\';
    }

    public function __call($name, $arguments)
    {
        // Handle getNamespace with a directory name
        if (Str::startsWith($name, 'getNamespace')) {
            $directory = str_replace('getNamespace', '', $name);

            return $this->getBaseNamespace().$directory.'\\';
        }

        // Return other calls
        return call_user_func_array(
            [$this, $name],
            $arguments
        );
    }
}
