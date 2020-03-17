<?php

namespace itechTest\Components\Routing;


use itechTest\App\Controllers\BaseController;
use itechTest\Components\Contracts\CanUseApplication;
use itechTest\Components\Routing\Exception\RouteNotFoundException;
use itechTest\Components\Routing\Exception\RouteResolvedToInvalidHttpMethodException;
use itechTest\Components\Routing\Exception\RouteResolvingException;

/**
 * Class Router
 *
 * @package itechTest\Components\Routing
 */
class Router extends CanUseApplication
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * Supported Http methods, no need to include PATCH, PUT, DELETE etct
     */
    private const HTTP_METHOD_GET  = 'GET';
    private const HTTP_METHOD_POST = 'POST';

    /**
     * @const array
     */
    private const SUPPORTED_HTTP_METHODS = [
        self::HTTP_METHOD_GET,
        self::HTTP_METHOD_POST,
    ];

    /**
     * @param string $pattern
     * @param array  $actionDetails
     *
     * @return array
     */
    private function validateActionsDetails(string $pattern, array $actionDetails): array
    {
        $controllerClass = array_get_item($actionDetails, 'controller');
        $methodName = array_get_item($actionDetails, 'method');

        if (empty($controllerClass)) {
            $errorMessage = "Controller Cannot Be Left Empty For Route With Pattern [$pattern]";
            throw RouteResolvingException::createWithMessage($errorMessage);
        }

        if (empty($methodName)) {
            $errorMessage = "Method Name Cannot Be Left Empty For Route With Pattern [$pattern]";
            throw RouteResolvingException::createWithMessage($errorMessage);
        }

        //if (!class_exists($controllerClass)) {
        //    $errorMessage = "Controller Class For [$pattern] - ($controllerClass) Does Not Exist";
        //    throw RouteResolvingException::createWithMessage($errorMessage);
        //}

        if (!method_exists($controllerClass, $methodName)) {
            $errorMessage = "Method Named [$methodName] Is Not Callable In The Controller";
            throw RouteResolvingException::createWithMessage($errorMessage);
        }

        // everything is good and can be called
        return $actionDetails;
    }

    /**
     * @return string
     */
    private function getRequestMethodUsedInRequest(): string
    {
        return (string)array_get_item($_SERVER, 'REQUEST_METHOD');
    }


    /**
     * This method will validate if the http method used in executing teh request is allowed or not
     */
    private function validateRequestMethod(): void
    {
        $requestMethod = $this->getRequestMethodUsedInRequest();

        if (!\in_array(strtoupper($requestMethod), self::SUPPORTED_HTTP_METHODS, true)) {
            $message = 'Invalid Or Non Supported Http Method Used For Request';
            throw RouteResolvedToInvalidHttpMethodException::createWithMessage($message);
        }
    }

    /**
     * @param string $method
     * @param string $pattern
     * @param array  $actionDetails
     * No need to support closures since the codes have been separated into controllers
     */
    private function route(string $method, $pattern, $actionDetails): void
    {
        // validate the supplied routes
        $this->validateActionsDetails($pattern, $actionDetails);

        // store the route
        $this->routes[$method][$pattern] = $actionDetails;
    }


    /**
     * @param string         $patten
     * @param BaseController $controller
     * @param string         $method
     */
    public function get(string $patten, BaseController $controller, string $method): void
    {
        $this->route(self::HTTP_METHOD_GET, $patten, compact('controller', 'method'));
    }


    /**
     * @param string $method
     *
     * @return array
     */
    private function getRoutesForHttpRequestMethod(string $method): array
    {
        return (array)array_get_item($this->routes, $method, []);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        // validate the Http method
        $this->validateRequestMethod();

        // get the set of patterns for the http method
        $requestMethod = $this->getRequestMethodUsedInRequest();
        $routes = $this->getRoutesForHttpRequestMethod($requestMethod);


        foreach ($routes as $pattern => $executioner) {
            [
                'matched'    => $matched,
                'parameters' => $parameters,
            ] = (new Request($_SERVER['REQUEST_URI']))->extractParameterWithPattern($pattern);

            if ($matched) {
                $executioner = array_values($executioner);
                $parameters = array_values($parameters);

                return \call_user_func_array($executioner, $parameters);
            }
        }

        throw RouteNotFoundException::createWithMessage('Route / Path Not Found');
    }

}