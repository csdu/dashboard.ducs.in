<?php
if (!function_exists('startsWith')) {
    function startsWith($string, $query)
    {
        return substr($string, 0, strlen($query)) === $query;
    }
}
