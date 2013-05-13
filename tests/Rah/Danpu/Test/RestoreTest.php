<?php

namespace Rah\Danpu\Test;
use Rah\Danpu\Dump;
use Rah\Danpu\Export;

class RestoreTest extends \PHPUnit_Framework_TestCase
{
    private $dump;

    public function __construct()
    {
        $this->dump = new Dump;
        $this->dump
            ->file(dirname(dirname(dirname(__DIR__))) . '/database.sql')
            ->dsn(\test_db_dsn)
            ->user(\test_db_user)
            ->pass(\test_db_pass)
            ->temp('/tmp');
    }

    public function testRestoreUncompressed()
    {
        new Import($this->dump);
    }
}