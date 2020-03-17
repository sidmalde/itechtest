<?php
/**
 * Created by PhpStorm.
 * User: adebola
 * Date: 15/08/2018
 * Time: 09:12
 */

namespace itechTest\Components\Social\Twitter\Request\Contracts;

/**
 * Interface CanInteractWithTwitterApiContract
 * @package itechTest\Components\Social\Twitter\Request\Contracts
 */
interface CanInteractWithTwitterApiContract
{
    public function setOption(string $name, $value): void;

    public function initiateRequest();

    public function getError();

    public function getHttpCode();

    public function getInfo(string $name);

    public function endRequest(): void;
}