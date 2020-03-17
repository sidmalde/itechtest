<?php

if (!function_exists('array_get')) {
    /**
     * @param array  $data
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    function array_get_item(array $data, string $key, $default = null)
    {
        return $data[$key] ?? $default;
    }
}


if (!function_exists('array_only_these_keys')) {

    /**
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    function array_only_these_keys(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }
}