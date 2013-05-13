<?php

class Rah_Danpu_Test_Export_Simple_SimpleTest extends PHPUnit_Framework_TestCase
{
    private $dump;
	private $temp;

    public function setUp()
    {
		$this->temp = tempnam('/tmp', 'st_');
        unlink($this->temp);
        $this->dump = new Rah_Danpu_Dump;
        $this->dump
            ->file($this->temp)
            ->dsn(test_db_dsn)
            ->user(test_db_user)
            ->pass(test_db_pass)
            ->tmp('/tmp');
    }

    public function testExportImport()
    {
        new Rah_Danpu_Export($this->dump);
        new Rah_Danpu_Import($this->dump);
        return file_exists($this->temp);
    }

    public function testIgnoredTable()
    {
        $this->dump->file(dirname(__FILE__) . '/dump.sql');
        new Rah_Danpu_Import($this->dump);
        $this->dump->file($this->temp);
        $this->dump->ignore(array('test_table_2'));
        new Rah_Danpu_Export($this->dump);
        return file_exists($this->temp) && strpos('test_table_2', file_get_contents($this->temp)) === false;
    }

    public function tearDown()
    {
        unlink($this->temp);
    }
}