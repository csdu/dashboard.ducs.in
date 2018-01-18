<?php
namespace DUCS;

class Template
{
    public static function render($_name, array $_args)
    {
        extract($_args);
        ob_start();
        require '../src/templates/' . $_name . '.phtml';
        return ob_get_clean();
    }
}
