<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;

$finder = DefaultFinder::create()
    ->in([__DIR__.'/app', __DIR__.'/tests']);

return Config::create()
    ->fixers(['-empty_return', 'short_array_syntax', 'ordered_use'])
    ->finder($finder);
