<?php

namespace itechTest\Components\Routing;

/**
 * Class Request
 *
 * @package itechTest\Components\Routing
 */
class Request
{
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $path;
    /**
     * This holds query string parsed into key value pair
     *
     * @var array
     */
    private $query = [];
    /**
     * @var string
     */
    private $scheme;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $queryString;
    /**
     * @var
     */
    private $fullUrl;

    /**
     * @var string
     */
    private $requestUri;

    /**
     * Request constructor.
     *
     * @param null|string $url
     */
    public function __construct(?string $url = null)
    {
        $this->parse($url);

        $this->requestUri = (string)array_get_item($_SERVER, 'REQUEST_URI');
    }

    /**
     * This will return the root url without query string or fragments
     *
     * @return string
     */
    public function rootUrl(): string
    {
        $authPrefix = '';
        $port = \in_array($this->port, [0, 80], false) ? '' : ":{$this->port}";
        if (!empty($this->getUsername()) || !empty($this->getPassword())) {
            $authPrefix = "{$this->username}:{$this->password}@";
        }
        return "{$this->scheme}://{$authPrefix}{$this->host}{$port}";
    }

    /**
     * @return array
     */
    public function segments(): array
    {
        $pathSegments = explode('/', $this->path);

        // remove empty
        $pathSegments = array_filter($pathSegments, function ($item) {
            return empty($item);
        });

        return array_values($pathSegments);
    }


    private function parseRequestUriIntoComponents(): void
    {
        $fullUrl = $this->getFullUrl();
        if (empty($fullUrl)) {
            return;
        }

        $pieces = parse_url($fullUrl) ?? [];
        $pieces = (array)$pieces;

        $this->scheme = array_get_item($pieces, 'scheme', '');
        $this->path = array_get_item($pieces, 'path', '');
        $port = (int)array_get_item($pieces, 'port');
        $this->setPort($port);
        $this->host = array_get_item($pieces, 'host', '');
        $this->username = array_get_item($pieces, 'user', '');
        $this->password = array_get_item($pieces, 'pass', '');
        $this->queryString = array_get_item($pieces, 'query', '');


        $this->parseQueryStringIntoQuery();
    }


    private function parseQueryStringIntoQuery(): void
    {
        $result = [];
        parse_str($this->queryString, $result);
        $this->query = $result;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        $port = (int)$this->port;
        $port = $port === 0 ? 80 : $port;
        return $port;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return (string)$this->host;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return (string)$this->path;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return (string)$this->scheme;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return (string)$this->queryString;
    }

    /**
     * @param null|string $url
     *
     * @return Request
     */
    public function parse(?string $url): self
    {
        if (null !== $url) {
            $this->fullUrl = $url;
        } else {
            $this->fullUrl = $this->constructUrl();
        }

        $this->parseRequestUriIntoComponents();
        //if (!empty($this->host)) {
        //}

        return $this;
    }

    /**
     * @param int $port
     *
     * @return Request
     */
    public function setPort(int $port): Request
    {
        $port = $port === 0 ? 80 : $port;
        $this->port = $port;
        return $this;
    }


    public function url($path): string
    {
        return $this->rootUrl() . '/' . ltrim($path, '/');
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }


    /**
     * @return string
     */
    public function constructUrl(): string
    {
        $serverName = (string)array_get_item($_SERVER, 'SERVER_NAME');
        // for docker support
        $serverName = str_replace('0.0.0.0', 'localhost', $serverName);
        $this->host = $serverName;

        $isHttps = isset($_SERVER['HTTPS']);
        $port = array_get_item($_SERVER, 'SERVER_PORT');
        $port = $port === 80 ? '' : ":$port";
        $scheme = 'http' . ($isHttps ? 's' : '');
        // rest of the uri
        $requestUri = array_get_item($_SERVER, 'REQUEST_URI');

        return "$scheme://$serverName{$port}{$requestUri}";
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    /**
     * @param mixed $fullUrl
     *
     * @return Request
     */
    public function setFullUrl($fullUrl): Request
    {
        $this->fullUrl = $fullUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullUrl()
    {
        return $this->fullUrl;
    }


    /**
     * @param string $pattern
     * The output is of the format ['matched'=>true/false, 'params'=>[]]
     *
     * @return array
     */
    public function extractParameterWithPattern(string $pattern): array
    {
        // clean up the pattern
        //$url = $this->getRequestUri();
        $url = $this->getFullUrl();
        $url = rtrim($url, '/');
        $pattern = rtrim($pattern, '/');

        // remove the query string from the uri before matching
        $url = preg_replace('/\?.*/', '', $url);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        $matched = preg_match($pattern, $url, $parameters) === 1;
        if ($matched) {
            // map the method/object pair to execute
            \array_shift($parameters);
        }
        return compact('matched', 'parameters');
    }
}
