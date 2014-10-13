<?php
return array(

	'default' => 'codeception',

	'connections' => array(
		'codeception'  => array(
			'driver'   => 'sqlite',
			'database' => dirname(dirname(dirname(__DIR__))).'/tests/_data/db.sqlite',
			'prefix'   => '',
		),
	),
);
