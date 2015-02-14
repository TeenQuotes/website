<?php namespace TeenQuotes\Tools;

use App, Controller, View;
use Illuminate\Http\Response;
use TeenQuotes\Http\JsonResponse;

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function responseIsNotFound(JsonResponse $r)
	{
		return $r->getStatusCode() == Response::HTTP_NOT_FOUND;
	}

	/**
	 * Test if we are in a testing environment
	 * @return boolean
	 */
	protected function isTestingEnvironment()
	{
		return in_array(App::environment(), ['testing', 'codeception']);
	}
}
