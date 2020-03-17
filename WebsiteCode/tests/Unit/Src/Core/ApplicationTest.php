<?php

namespace itechTest\Tests\Unit\Src\Core;


use itechTest\Tests\TestCase;

/**
 * Class ApplicationTest
 *
 * @package itechTest\Tests\Unit\Src\Core
 */
class ApplicationTest extends TestCase
{

    /**
     * @test
     */
    public function when_TestIsRunning_Then_IsRunningInCommandLineIsTrue(): void
    {
        // Arrange
        $expected = true;

        // Act
        $actual = $this->getApp()->isRunningInCli();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function when_getViewPath_Then_CorrectValueIsReturned(): void
    {
        // Arrange
        $basePath = $this->getApp()->getBasePath();
        $expected = normalizePath($basePath.'/resources/views');

        // Act
        $actual = $this->getApp()->getViewPath();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function when_getThemePath_Then_CorrectValueIsReturned(): void
    {
        // Arrange
        $basePath = $this->getApp()->getBasePath();
        $expected = normalizePath($basePath.'/resources/themes');

        // Act
        $actual = $this->getApp()->getThemePath();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function when_getConfigFolderPath_Then_CorrectValueIsReturned(): void
    {
        // Arrange
        $basePath = $this->getApp()->getBasePath();
        $expected = normalizePath($basePath.'/configs');

        // Act
        $actual = $this->getApp()->getConfigFolderPath();

        // Assert
        $this->assertEquals($expected, $actual);
    }
}