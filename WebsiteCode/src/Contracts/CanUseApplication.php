<?php

namespace itechTest\Components\Contracts;


use itechTest\Components\Core\Application;

/**
 * Class CanUseApplication
 * @package itechTest\Components\Contracts
 */
abstract class CanUseApplication
{
    /**
     * @var Application
     */
    private $application;

    /**
     * CanUseApplication constructor.
     *
     * @param null|Application $application
     */
    public function __construct(?Application $application = null)
    {
        $this->application = $application ?? Application::getInstance();
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }
}