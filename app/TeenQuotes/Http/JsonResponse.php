<?php namespace TeenQuotes\Http;

use Illuminate\Http\JsonResponse as JsonResponseOriginal;
use Illuminate\Support\Contracts\ArrayableInterface;

class JsonResponse extends JsonResponseOriginal {

	/**
	 * The original data
	 * @var mixed
	 */
	protected $originalData;

	/**
	 * Constructor.
	 *
	 * @param  mixed  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @param  int    $options
	*/
	public function __construct($data = null, $status = 200, $headers = array(), $options = 0)
	{
		// We just need to be able to retrieve the original data
		$this->originalData = $data;

		if ($data instanceof ArrayableInterface)
			$data = $data->toArray();

		parent::__construct($data, $status, $headers, $options);
	}

	public function getOriginalData()
	{
		return $this->originalData;
	}
}