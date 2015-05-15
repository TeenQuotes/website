<?php

namespace TeenQuotes\Tools;

use App;
use Controller;
use Illuminate\Http\Response;
use TeenQuotes\Http\JsonResponse;
use View;

class BaseController extends Controller
{
    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function responseIsNotFound(JsonResponse $r)
    {
        return $r->getStatusCode() == Response::HTTP_NOT_FOUND;
    }

    /**
     * Test if we are in a testing environment.
     *
     * @return bool
     */
    protected function isTestingEnvironment()
    {
        return in_array(App::environment(), ['testing', 'codeception']);
    }
}
