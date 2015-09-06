<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Http\Facades;

use Illuminate\Support\Facades\Response as ResponseFacadeOriginal;
use TeenQuotes\Http\JsonResponse;

class Response extends ResponseFacadeOriginal
{
    /**
     * Return a new JSON response from the application.
     *
     * @param string|array $data
     * @param int          $status
     * @param array        $headers
     * @param int          $options
     *
     * @return \TeenQuotes\Http\JsonResponse
     */
    public static function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }
}
