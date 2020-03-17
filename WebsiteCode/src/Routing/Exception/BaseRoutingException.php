<?php

namespace itechTest\Components\Routing\Exception;


use RuntimeException;

/**
 * Class BaseRoutingException
 *
 * @package itechTest\Components\Routing\Exception
 */
abstract class BaseRoutingException extends RuntimeException
{
    /**
     * @param string $message
     * @param int    $code
     *
     * @return self
     */
    public static function createWithMessage(string $message, $code = 500): self
    {
        return new static($message, $code);
    }
}