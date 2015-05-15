<?php

namespace TeenQuotes\Robots\Controllers;

use BaseController;
use Healey\Robots\Robots;
use Illuminate\Foundation\Application as App;
use Response;

class RobotsController extends BaseController
{
    /**
     * @var Robots
     */
    private $robots;

    /**
     * @var App
     */
    private $app;

    /**
     * @param Robots $robots
     * @param App    $app
     */
    public function __construct(Robots $robots, App $app)
    {
        $this->robots = $robots;
        $this->app = $app;
    }

    public function getRobots()
    {
        $response = $this->constructResponse($this->app->environment());

        return Response::make($response, 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * Generate the robots.txt file.
     *
     * @param string $env The app environment
     *
     * @return Response
     */
    private function constructResponse($env)
    {
        switch ($env) {
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
