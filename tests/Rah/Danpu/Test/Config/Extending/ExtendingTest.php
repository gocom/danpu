<?php

namespace Rah\Danpu\Test\Config\Extending;
use Rah\Danpu\Dump;
use Rah\Danpu\Export;
use Rah\Danpu\Config;

class MyConfig extends Config
{
    public function __construct()
    {
        $this->file = tempnam('/tmp', 'st_');
        $this->dsn = \test_db_dsn;
        $this->user = \test_db_user;
        $this->pass = \test_db_pass;
        $this->tmp = '/tmp';
    }
}

class ExtendingTest extends \PHPUnit_Framework_TestCase
{
    private $dump;

    public function setUp()
    {
		$this->dump = new MyConfig();
        unlink($this->dump->file);
    }

    public function testExtending()
    {
        new Export($this->dump);
        $this->assertTrue(file_exists($this->dump->file));
    }

    public function tearDown()
    {
        @unlink($this->dump->file);
    }
}
