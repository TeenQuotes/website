<?php
namespace TeenQuotes\Exception;

use Illuminate\Exception\PlainDisplayer as BasePlainDisplayer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Exception;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PlainDisplayer extends BasePlainDisplayer {

	/**
	 * Display the given exception to the user.
	 *
	 * @param  \Exception  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function display(Exception $exception)
	{
		$status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

		$headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();

		// Check if the view exists
		$viewName = Config::get('exceptions.'.$status);
		if (View::exists($viewName))
			return Response::view($viewName, array(), $status);

		// The view was not found, fall back to the default view
		return parent::display($exception);
	}
}