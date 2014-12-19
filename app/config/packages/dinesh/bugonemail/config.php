<?php

return array(
	'project_name'       => Lang::get('layout.nameWebsite'),
	'notify_emails'      => ['antoine.augusti@teen-quotes.com'],
	'email_template'     => "bugonemail::email.notifyException",
	'notify_environment' => ['staging', 'production'],
	'prevent_exception'  => ['Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException'],
);
