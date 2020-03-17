<?php

namespace itechTest\Tests\Unit\Src\Routing;

use itechTest\Components\Routing\Request;
use itechTest\Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 12/08/2018
 * Time: 22:49
 */
class RequestTest extends TestCase
{

    /**
     * @var Request
     */
    private $request;

    protected function setUp()
    {
        parent::setUp();
        $this->request = new Request();
    }

    /**
     *
     * @return array
     */
    public function dataProviderForRequests(): array
    {
        // format of array item [url, scheme, username, password, port, path, queryString, query]

        return [
            ['http://www.google.com', 'http', '', '', 80, '', '', []],
            ['http://www.ab.com?c=d&e=f', 'http', '', '', 80, '', 'c=d&e=f', ['c' => 'd', 'e' => 'f']],
            ['ftp://www.cd.com:90/a/b/c', 'ftp', '', '', 90, '/a/b/c', '', []],
            ['https://www.google.com:7000/', 'https', '', '', 7000, '/', '', []],
            ['https://u:p@c.com:7010/', 'https', 'u', 'p', 7010, '/', '', []],
        ];
    }

    /**
     * @test
     *
     * @param string $url
     * @param string $scheme
     * @param string $username
     * @param string $password
     * @param int    $port
     * @param string $path
     * @param string $queryString
     * @param array  $query
     *
     * @dataProvider dataProviderForRequests
     */
    public function when_UrlIsProvidedAndParsedIntoRequest_Then_TheComponentsAreCorrectlyObtained(
        string $url,
        string $scheme,
        string $username,
        string $password,
        int $port,
        string $path,
        string $queryString,
        array $query
    ): void {
        // Arrange
        $this->request->parse($url);

        // Act
        $actualFullUrl = $this->request->getFullUrl();
        $actualPort = $this->request->getPort();
        $actualScheme = $this->request->getScheme();
        $actualPath = $this->request->getPath();
        $actualUsername = $this->request->getUsername();
        $actualPassword = $this->request->getPassword();
        $actualQueryString = $this->request->getQueryString();
        $actualQuery = $this->request->getQuery();

        // Assert
        $this->assertEquals($url, $actualFullUrl);
        $this->assertEquals($scheme, $actualScheme);
        $this->assertEquals($username, $actualUsername);
        $this->assertEquals($password, $actualPassword);
        $this->assertEquals($port, $actualPort);
        $this->assertEquals($path, $actualPath);
        $this->assertEquals($queryString, $actualQueryString);
        $this->assertEquals($query, $actualQuery);
    }


    /**
     * @test
     */
    public function when_GetRootUrl_Then_ReturnedValueDoesNotEndWithASlash(): void
    {
        // Arrange
        $url = 'http://dance.com/a/b/c?v=23';
        $expected = 'http://dance.com';

        // Act
        $actual = $this->request->parse($url)->rootUrl();

        // Assert
        $this->assertEquals($expected, $actual);
        $this->assertStringEndsNotWith('/', $actual);
    }


    /**
     *
     * @return array
     */
    public function dataProviderForSetUrlWithRequest(): array
    {
        // format of array item [url, scheme, port, path, queryString, query]

        return [
            ['http://www.google.com', 'home/way', 'http://www.google.com/home/way'],
            ['http://www.google.com:90', 'home/way', 'http://www.google.com:90/home/way'],
            [
                'http://username:password@www.ab.com?c=d&e=f',
                '/list',
                'http://username:password@www.ab.com/list',
            ],
        ];
    }


    /**
     * @test
     *
     * @dataProvider dataProviderForSetUrlWithRequest
     *
     * @param string $url
     * @param string $relativeUrl
     * @param string $expected
     */
    public function when_SetUrl_Then_ReturnedValueIsCorrect(string $url, string $relativeUrl, string $expected): void
    {
        // Arrange

        // Act
        $actual = $this->request->parse($url)->url($relativeUrl);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function dataProviderForRouteMatching(): array
    {
        // format [pattern, url, matched, params]
        return [
            ['/dance/(\d+)', '/dance/23', true, ['23']],
            ['/dance/(\w+)/(\d+)', '/dance/koko/21', true, ['koko', '21']],
            ['/dance/(\w+)/', '/dance/23', true, ['23']],
            ['/dance/(\w+)/', '/dance/23/', true, ['23']],
            ['/dance/(\d+)', '/dance/23person', false, []],
        ];
    }

    /**
     * @dataProvider dataProviderForRouteMatching
     * @test
     *
     * @param string $pattern
     * @param string $uri
     * @param bool   $matched
     * @param array  $params
     */
    public function when_ExtractParameterWithPatternForRoutes_Then_CorrectEntriesAreReturned(
        string $pattern,
        string $uri,
        bool $matched,
        array $params
    ): void {
        // Arrange
        $request = new Request($uri);

        // Act
        $actual = $request->extractParameterWithPattern($pattern);
        ['matched' => $actualMatched, 'parameters' => $actualParameters] = $actual;

        // Assert
        $this->assertEquals($actualMatched, $matched);
        $this->assertEquals($actualParameters, $params);
    }
}