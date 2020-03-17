<?php

namespace itechTest\Components\Social\Twitter\Exceptions;


use RuntimeException;

/**
 * Class TwitterManagerRequestException
 *
 * @package itechTest\Components\Social\Twitter\Exceptions
 */
class TwitterManagerRequestException extends RuntimeException
{

    private $errorResponse;

    /**
     * @param string $message
     *
     * @param int    $code
     * @param        $response
     *
     * @return TwitterManagerRequestException
     */
    public static function createWithResponse(string $message, int $code, $response): TwitterManagerRequestException
    {
        $exception = new self($message, $code);
        $exception->errorResponse = $response;
        return $exception;
    }

    /**
     * @return mixed
     */
    public function getErrorResponse()
    {
        return $this->errorResponse;
    }
}