<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Http;

use Illuminate\Http\JsonResponse as JsonResponseOriginal;
use Illuminate\Support\Contracts\ArrayableInterface;

class JsonResponse extends JsonResponseOriginal
{
    /**
     * The original data.
     *
     * @var mixed
     */
    protected $originalData;

    /**
     * Constructor.
     *
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        // We just need to be able to retrieve the original data
        $this->originalData = $data;

        if ($data instanceof ArrayableInterface) {
            $data = $data->toArray();
        }

        parent::__construct($data, $status, $headers, $options);
    }

    public function getOriginalData()
    {
        return $this->originalData;
    }
}
