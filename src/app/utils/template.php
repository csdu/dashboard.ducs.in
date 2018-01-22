<?php

// assets mapping from source to hashed
$assets = json_decode(file_get_contents($templatesDir . 'assets.json'), true);

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

// maps a plain asset path to hashed path
$mapAsset = function($name) use ($assets)
{
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $mapExtToFolder = [
        'sass' => 'css',
        'js' => 'js',
        'css' => 'css',
    ];
    return '/assets/' . $mapExtToFolder[$ext] . '/' . $assets[$name];
};
