<?php
return array(

	'default' => 'sqlite',

	'connections' => array(
		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		),
		
		'codeception'  => array(
			'driver'   => 'sqlite',
			'database' => dirname(dirname(__DIR__)).'/tests/_data/db.sqlite',
			'prefix'   => '',
		),
	),
);
