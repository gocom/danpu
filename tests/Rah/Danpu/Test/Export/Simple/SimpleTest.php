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
            ->temp('/tmp');
    }

    public function testExportImport()
    {
        new Export($this->dump);
        new Import($this->dump);
        return file_exists($this->temp);
    }

    public function tearDown()
    {
        unlink($this->temp);
    }
}