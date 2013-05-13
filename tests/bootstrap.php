<?php

error_reporting(E_ALL);

include dirname(__DIR__) . '/vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('Rah\Danpu\Test', __DIR__);
$loader->add('Rah\Danpu', dirname(__DIR__) . '/src');