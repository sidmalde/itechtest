<?php

namespace itechTest\App\Controllers;

use itechTest\Components\Core\Application;


/**
 * Class BaseController
 *
 * @package itechTest\App\Controllers
 */
abstract class BaseController
{
    /**
     * @var Application
     */
    private $application;

    /**
     * Parameters from the matched route
     *
     * @var array
     */
    protected $routeSegments = [];

    /**
     * Class constructor
     *
     * @param Application $application
     * @param array       $routeSegments This will be the segments extracted from the url
     */
    public function __construct(Application $application, array $routeSegments = [])
    {
        $this->routeSegments = $routeSegments;

        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }
}
