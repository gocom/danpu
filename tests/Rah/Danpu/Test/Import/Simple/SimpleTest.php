<?php

namespace Rah\Danpu\Test\Import\Simple;
use Rah\Danpu\Dump;
use Rah\Danpu\Import;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    private $dump;

    public function __construct()
    {
        $this->dump = new Dump;
        $this->dump
            ->file(__DIR__ . '/dump.sql')
            ->dsn(\test_db_dsn)
            ->user(\test_db_user)
            ->pass(\test_db_pass)
            ->tmp('/tmp');
    }

    public function testRestoreUncompressed()
    {
        new Import($this->dump);
    }
}