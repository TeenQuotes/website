<?php

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
