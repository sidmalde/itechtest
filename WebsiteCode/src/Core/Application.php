<?php

namespace itechTest\Components\Core;


use itechTest\Components\Configuration\ConfigurationManager;
use itechTest\Components\Injection\Container;
use itechTest\Components\Routing\Request;
use itechTest\Components\Social\Twitter\TwitterManager;
use itechTest\Components\Views\ViewHandler;

/**
 * Class Application
 *
 * @package itechTest\Components\Core
 */
class Application extends Container
{
    /**
     * @var string
     */
    private $basePath;
    private $viewPath;
    private $themePath;
    private $configPath;

    /**
     * this will indicate if the application is running in command line
     *
     * @var bool
     */
    private $isRunningInCli = false;

    /**
     * Application constructor.
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        // set the application as the container
        self::setInstance($this);

        $this->isRunningInCli = strcasecmp(php_sapi_name(), 'cli') === 0;
        $this->basePath = $basePath;
        $this->setUsefulPaths();
        $this->initiateCoreComponents();
    }

    /**
     * This method will set the necessary paths that are needed
     */
    private function setUsefulPaths(): void
    {
        $basePath = $this->getBasePath();
        $this->viewPath = normalizePath($basePath . DIRECTORY_SEPARATOR . 'resources/views');
        $this->themePath = normalizePath($basePath . DIRECTORY_SEPARATOR . 'resources/themes');
        $this->configPath = normalizePath($basePath . DIRECTORY_SEPARATOR . 'configs');
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return rtrim($this->basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * This method will set the core components to use in the application
     */
    public function initiateCoreComponents(): void
    {
        // load the view handler
        $this['view'] = new ViewHandler($this);

        // load the configurations
        $configurationManager = new ConfigurationManager($this);
        $configurationManager->loadConfigurations();
        $this['config'] = $configurationManager;

        $this['currentDate'] = function () {
            return new \DateTime();
        };


        // Add Request
        $this['request'] = new Request();

        // Add the session manager if not running in command line
        // not needed since I am not implementing authentication, so i'm turning it off
        /*
        if (!$this->isRunningInCli) {

        $sessionManager = new SessionManager();
        $this['session'] = $sessionManager;

        }
        */

        /*
         * Setup Twitter
         */
        $consumer_key = $configurationManager->get('twitter.consumer_key');
        $consumer_secret = $configurationManager->get('twitter.consumer_secret');
        $oauth_access_token = $configurationManager->get('twitter.access_token');
        $oauth_access_token_secret = $configurationManager->get('twitter.access_token_secret');

        $this['twitter'] = function () use (
            $consumer_key,
            $consumer_secret,
            $oauth_access_token,
            $oauth_access_token_secret
        ) {
            return new TwitterManager($consumer_key, $consumer_secret, $oauth_access_token,
                $oauth_access_token_secret);
        };

    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * @return string
     */
    public function getConfigFolderPath(): string
    {
        return $this->configPath;
    }

    /**
     * @return mixed
     */
    public function getThemePath(): string
    {
        return $this->themePath;
    }

    /**
     * @return bool
     */
    public function isRunningInCli(): bool
    {
        return $this->isRunningInCli;
    }
}