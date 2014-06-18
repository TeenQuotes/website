<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug'            => true,
	
	'users.avatarPath' => 'public/uploads/avatar',
	
	'domain'           => 'dev.tq',
	
	'domainAPI'        => 'api.dev.tq',
	'domainStories'    => 'stories.dev.tq',

	'connections' => array(
		'mysql' => array(
			'driver'      => 'mysql',
			'host'        => 'localhost',
			'database'    => 'teenquotes',
			'username'    => 'root',
			'password'    => 'helloworld',
			'charset'     => 'utf8',
			'collation'   => 'utf8_unicode_ci',
			'prefix'      => '',
			'unix_socket' => '/tmp/mysql.sock',
		),
	),
);