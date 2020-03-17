<?php

namespace itechTest\Tests;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use itechTest\Components\Core\Application;

/**
 * Class TestCase
 */
abstract class TestCase extends PhpUnitTestCase
{

    /**
     * @var Application
     */
    private $app;

    /**
     * @return Application
     */
    protected function getApp(): Application
    {
        return $this->app;
    }

    protected function setUp()
    {
        parent::setUp();

        $basePath = \dirname(__DIR__ . '/../');
        $this->app = new Application($basePath);
    }

    protected function tearDown()
    {
        parent::tearDown();

        // remove the app instance
        $this->app = null;
    }
}