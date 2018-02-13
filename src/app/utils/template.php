<?php

// assets mapping from source to hashed
if (ASSETS_HOST_NAME === 'http://localhost:8000/') {
    $assets = json_decode(file_get_contents($templatesDir . 'assets.json'), true);
} else {
    require_once $templatesDir . 'assets.php';
}

// allows including files from templates/partials into template files
function partials($p, $locals = [])
{
    $templatesDir = getcwd() . '/../src/templates/';
    extract($locals, EXTR_SKIP);
    include $templatesDir . 'partials/' . $p . '.phtml';
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
    return ASSETS_HOST_NAME . $mapExtToFolder[$ext] . '/dash/' . $assets[$name];
};
