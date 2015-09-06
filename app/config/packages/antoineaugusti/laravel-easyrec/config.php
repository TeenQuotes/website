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
    'baseURL'    => 'https://demo.easyrec.org',
    'apiVersion' => '1.0',
    'apiKey'     => Config::get('services.easyrec.apiKey'),
    'tenantID'   => Config::get('services.easyrec.tenantID'),
];
