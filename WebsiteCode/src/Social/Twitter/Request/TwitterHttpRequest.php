<?php
/**
 * Created by PhpStorm.
 * User: adebola
 * Date: 15/08/2018
 * Time: 09:02
 */

namespace itechTest\Components\Social\Twitter\Request;

use itechTest\Components\Social\Twitter\Request\Contracts\CanInteractWithTwitterApiContract;

/**
 * Class TwitterHttpRequest
 * @package itechTest\Components\Social\Twitter\Request
 */
class TwitterHttpRequest implements CanInteractWithTwitterApiContract
{
    private $curlInstance = null;

    /**
     * TwitterHttpRequest constructor.
     */
    public function __construct()
    {
        $this->curlInstance = curl_init();
    }


    /**
     * @param string $name
     * @param $value
     */
    public function setOption(string $name, $value): void
    {
        curl_setopt($this->curlInstance, $name, $value);
    }

    /**
     * @return mixed
     */
    public function initiateRequest()
    {
        return curl_exec($this->curlInstance);
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return curl_error($this->curlInstance);
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->getInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getInfo(string $name)
    {
        return curl_getinfo($this->curlInstance, $name);
    }

    public function endRequest(): void
    {
        curl_close($this->curlInstance);
    }
}