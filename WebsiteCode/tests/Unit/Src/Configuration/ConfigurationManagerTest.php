<?php

namespace itechTest\Tests\Unit\Src\Configuration;


use itechTest\Components\Configuration\ConfigurationManager;
use itechTest\Tests\TestCase;

/**
 * Class ConfigurationManagerTest
 *
 * @package itechTest\Tests\Unit\Src\Configuration
 */
class ConfigurationManagerTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @param array $configs
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|ConfigurationManager
     */
    private function createConfigFile(array $configs)
    {
        $mockObject = $this->getMockBuilder(ConfigurationManager::class)
            ->setMethods(['getRawConfiguration'])
            ->getMock();
        $mockObject->method('getRawConfiguration')
            ->willReturn($configs);
        return $mockObject;
    }


    public function dataProviderForConfig()
    {
        $configs = [
            'app.name'   => 'name',
            'app.age'    => 23,
            'app.school' => 'BCS',
            'app.gender' => 'male',
        ];
        return [
            ['app.name', 'name', $configs],
            ['app.age', 23, $configs],
            ['app.school', 'BCS', $configs],
            ['app.gender', 'male', $configs],
            ['app.father', null, $configs],
            ['school.app', null, $configs],
        ];
    }

    /**
     * @test
     *
     * @param string $key
     * @param        $expected
     * @param array  $configs
     *
     * @dataProvider dataProviderForConfig
     */
    public function when_GetConfig_Then_CorrectValueIsReturned(string $key, $expected, array $configs): void
    {
        // Arrange
        $configManager = $this->createConfigFile($configs);

        // Act
        $actual = $configManager->get($key);

        // Assert
        $this->assertEquals($expected, $actual);
    }
}