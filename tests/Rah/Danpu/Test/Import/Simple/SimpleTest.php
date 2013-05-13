<?php

class Rah_Danpu_Test_Import_Simple_SimpleTest extends PHPUnit_Framework_TestCase
{
    private $dump;

    public function __construct()
    {
        $this->dump = new Rah_Danpu_Dump;
        $this->dump
            ->file(dirname(__FILE__) . '/dump.sql')
            ->dsn(test_db_dsn)
            ->user(test_db_user)
            ->pass(test_db_pass)
            ->tmp('/tmp');
    }

    public function testRestoreUncompressed()
    {
        new Rah_Danpu_Import($this->dump);
    }
}