<?php

namespace Examples;
use Rah\Danpu\Dump;
use Rah\Danpu\Config;
use Rah\Danpu\Export;
use Rah\Danpu\Import;

// Include Composer's autoloader. You can also use
// any other PSR-0 compatible autoloader. Just point
// the autoloader to the '../src' directory with the
// namespace of Rah\Danpu.

include '../vendor/autoload.php';

// Creates a new instance of Rah\Danpu\Dump.

$dump = new Dump;
$dump
    ->file(__DIR__ . '/file.sql')
    ->dsn('mysql:dbname=database;host=localhost')
    ->user('username')
    ->pass('password')
    ->tmp('/tmp');

// Exports the database.

new Export($dump);

// Imports the database.

new Import($dump);

// Alternatively you could create a personalized config instance
// by extending Config and then passing it to Dump through
// the constructor.

class MyAppConfig extends Config
{
    public $file = 'file.sql';
    public $dsn = 'mysql:dbname=database;host=localhost';
    public $user = 'username';
    public $pass = 'password';
    public $tmp = '/tmp';
}

// Export again using MyAppConfig.

new Export(new Dump(new MyAppConfig));

// Or import.

new Import(new Dump(new MyAppConfig));
