<?php
namespace DUCS;

class Template
{
    public static function render($_name, array $_args)
    {
        $templatesDir = getcwd() . '/../src/templates/';
        extract($_args);
        ob_start('ob_gzhandler');
        require 'utils/template.php';
        require  $templatesDir . $_name . '.phtml';
        return ob_get_clean();
    }
}
