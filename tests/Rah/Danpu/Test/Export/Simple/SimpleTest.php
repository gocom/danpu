<?php

namespace Rah\Danpu\Test\Export\Simple;
use Rah\Danpu\Dump;
use Rah\Danpu\Export;
use Rah\Danpu\Import;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    private $dump;
	private $temp;

    public function setUp()
    {
		$this->temp = tempnam('/tmp', 'st_');
        unlink($this->temp);
        $this->dump = new Dump;
        $this->dump
            ->file($this->temp)
            ->dsn(\test_db_dsn)
            ->user(\test_db_user)
            ->pass(\test_db_pass)
            ->tmp('/tmp');
    }

    public function testExportImport()
    {
        new Export($this->dump);
        new Import($this->dump);
        return file_exists($this->temp);
    }

    public function testIgnoredTable()
    {
        $this->dump->file(__DIR__ . '/dump.sql');
        new Import($this->dump);
        $this->dump->file($this->temp);
        $this->dump->ignore(array('test_table_2'));
        new Export($this->dump);
        return file_exists($this->temp) && strpos('test_table_2', file_get_contents($this->temp)) === false;
    }

    public function tearDown()
    {
        unlink($this->temp);
    }
}
