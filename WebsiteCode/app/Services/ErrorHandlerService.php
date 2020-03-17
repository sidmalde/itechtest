<?php

namespace itechTest\App\Services;

/**
 * Class ErrorHandlerService
 *
 * @package itechTest\App\Services
 */
class ErrorHandlerService
{

    private const REPORTING_LEVEL = E_ALL;

    public static function initiateErrorHandler(): void
    {
        error_reporting(self::REPORTING_LEVEL);
        set_error_handler([static::class, 'handleAppErrors']);
        set_exception_handler([static::class, 'handleAppExceptions']);
    }

    /**
     * @param $level
     * @param $message
     * @param $file
     * @param $line
     *
     * @throws \ErrorException
     */
    public static function handleAppErrors($level, $message, $file, $line): void
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public static function handleAppExceptions(\Throwable $exception): void
    {

        $class = \get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $template = '';
        $template .= '<div>';
        $template .= '<h1>Exception Thrown</h1>';
        $template .= '<ul>';
        $template .= "<li><strong>Exception Class</strong>: $class</li>";
        $template .= "<li><strong>Message</strong> : $message</li>";
        $template .= "<li><strong>File</strong>: $file</li>";
        $template .= "<li><strong>Line</strong>: $line</li>";
        $template .= '</ul>';
        $template .= '</div>';

        echo $template;
    }

}