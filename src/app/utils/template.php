<?php
// allows including files from templates/partials into template files
function partials($p)
{
    $templatesDir = getcwd() . '/../src/templates/';
    require $templatesDir . 'partials/' . $p . '.phtml';
};

// returns default if current variable is not set/empty
function defaults($var, $defaults)
{
    return (!isset($var) || empty($var))
        ? $defaults
        : $var;
}
