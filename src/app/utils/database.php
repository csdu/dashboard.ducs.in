<?php
if (!function_exists('userHashToSecret')) {
    function userHashToSecret($hash)
    {
        return base_convert(strrev(substr($hash, 20)), 16, 32);
    }
}

if (!function_exists('formatEmail')) {
    function formatEmail($str)
    {
        $temp = explode('@gmail.com', $str)[0];
        return str_replace('.', '' , $temp);
    }
}

if (!function_exists('encryptPassword')) {
    function encryptPassword($plain)
    {
        return md5($plain);
    }
}
