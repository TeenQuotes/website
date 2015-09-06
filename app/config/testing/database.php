<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    'default' => 'sqlite',

    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ],

        'codeception'  => [
            'driver'   => 'sqlite',
            'database' => dirname(dirname(__DIR__)).'/tests/_data/db.sqlite',
            'prefix'   => '',
        ],
    ],
];
