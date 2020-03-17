<?php
if (!function_exists('url')) {
    /**
     * @param string $path
     *
     * @return string
     */
    function url($path = ''): string
    {
        return application()['request']->url($path);
    }
}

if (!function_exists('asset')) {
    /**
     * @param string $path
     *
     * @return string
     */
    function asset($path = ''): string
    {
        $path = 'assets/' . ltrim($path, '/');
        return application()['request']->url($path);
    }
}