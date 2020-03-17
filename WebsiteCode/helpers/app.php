<?php

use itechTest\Components\Configuration\ConfigurationManager;
use itechTest\Components\Core\Application;
use itechTest\Components\Injection\Container;
use itechTest\Components\Views\ViewHandler;


if (!function_exists('application')) {
    /**
     * @return Application
     */
    function application(): Container
    {
        return Application::getInstance();
    }
}

if (!function_exists('view')) {
    /**
     * @return ViewHandler
     */
    function view(): ViewHandler
    {
        return \application()['view'];
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        /**
         * @var ConfigurationManager $config
         */
        $config = \application()['config'];
        return $config->get($key, $default);
    }
}