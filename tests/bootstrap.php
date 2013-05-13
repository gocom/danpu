<?php

error_reporting(E_ALL);

include dirname(dirname(__FILE__)) . '/vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('Rah_Danpu_Test', dirname(__FILE__));
$loader->register();