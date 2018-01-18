<?php
if (!function_exists('startsWith')) {
    function startsWith($string, $query)
    {
        return substr($string, 0, strlen($query)) === $query;
    }
}

if (!function_exists('userHashToSecret')) {
    function userHashToSecret($hash)
    {
        return base_convert(strrev(substr($hash, 20)), 16, 32);
    }
}
