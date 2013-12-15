<?php

error_reporting(E_ALL);

include dirname(__DIR__) . '/vendor/autoload.php';

set_error_handler(function ($errno, $errstr, $errfile, $errline)
{
    throw new Exception($errstr . ' in ' . $errfile . ' on line ' . $errline);
});

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('Rah\Danpu\Test', __DIR__);
$loader->register();
