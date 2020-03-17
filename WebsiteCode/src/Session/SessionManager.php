<?php

namespace itechTest\Components\Session;

/**
 * Class SessionManager
 *
 * @package itechTest\Components\Session
 */
class SessionManager
{

    /**
     * SessionManager constructor.
     */
    public function __construct()
    {
        $this->startSession();
    }

    /**
     * Start the session
     */
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function __destruct()
    {
        $this->endSession();
    }

    private function endSession(): void
    {
        session_destroy();
    }
}