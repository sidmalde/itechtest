<?php
/**
 * Created by PhpStorm.
 * User: adebola
 * Date: 15/08/2018
 * Time: 09:40
 */

namespace itechTest\Components\Social\Twitter\Exceptions;

/**
 * Class TwitterManagerMissingHandleException
 * @package itechTest\Components\Social\Twitter\Exceptions
 */
class TwitterManagerMissingHandleException extends \LogicException
{
    protected $message = 'You need to set a twitter handle before executing a request';
}