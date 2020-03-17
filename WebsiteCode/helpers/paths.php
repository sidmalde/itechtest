<?php

if (!function_exists('normalizePath')) {

    /**
     * @param string $path
     *
     * @return string
     */
    function normalizePath(string $path): string
    {
        return str_replace(["\\", '/'], DIRECTORY_SEPARATOR, $path);
    }
}


if (!function_exists('joinPaths')) {
    /**
     * @param array $paths
     *
     * @return string
     */
    function joinPaths(array $paths): string
    {
        $joinedPath = implode(DIRECTORY_SEPARATOR, $paths);
        return normalizePath($joinedPath);
    }
}