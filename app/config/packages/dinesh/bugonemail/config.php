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
    'project_name'       => Lang::get('layout.nameWebsite'),
    'notify_emails'      => ['antoine.augusti@teen-quotes.com'],
    'email_template'     => 'bugonemail::email.notifyException',
    'notify_environment' => ['staging', 'production'],
    'prevent_exception'  => ['Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException'],
];
