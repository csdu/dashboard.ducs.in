<?php
if (!function_exists('userHashToSecret')) {
    function userHashToSecret($hash)
    {
        return base_convert(strrev(substr($hash, 20)), 16, 32);
    }
}
