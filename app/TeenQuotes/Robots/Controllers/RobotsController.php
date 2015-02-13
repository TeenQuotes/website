<?php namespace TeenQuotes\Robots\Controllers;

use BaseController, Response;
use Illuminate\Foundation\Application as App;
use Healey\Robots\Robots;

class RobotsController extends BaseController {

	/**
	 * @var \Healey\Robots\Robots
	 */
	private $robots;

	private $app;

	public function __construct(Robots $robots, App $app)
	{
		$this->robots = $robots;
		$this->app = $app;
	}

	public function getRobots()
	{
		$respnse = $this->constructResponse($this->app->environment());

		return Response::make($this->robots->generate(), 200, ['Content-Type' => 'text/plain']);
	}

	private function constructResponse($env)
	{
		switch ($env)
		{
			// If on the live server, serve a nice, welcoming robots.txt
			case 'production':
				$response = $this->robots->addUserAgent('*');
				$response .= $this->robots->addAllow('/');
				break;

			// If you're on any other server, tell everyone to go away
			default:
				$response = $this->robots->addUserAgent('*');
				$response .= $this->robots->addDisallow('/');
		}

		return $this->robots->generate();
	}
}