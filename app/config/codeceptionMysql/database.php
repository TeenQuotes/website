<?php

return [

    'default' => 'mysql',

    'connections' => [
        'mysql'  => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'codeception',
            'username'  => 'travis',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],
];
