<?php

return [

    'default' => 'codeception',

    'connections' => [
        'codeception'  => [
            'driver'   => 'sqlite',
            'database' => dirname(dirname(dirname(__DIR__))).'/tests/_data/db.sqlite',
            'prefix'   => '',
        ],
    ],
];
