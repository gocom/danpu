<?php

namespace Rah\Danpu\Test;

use Rah\Danpu\Dump;
use Rah\Danpu\Export;
use Rah\Danpu\Import;

class BasicTest extends \PHPUnit_Framework_TestCase
{
    private $dump;
    private $target;

    /**
     * @dataProvider provider
     */

    public function testDump($source, $target)
    {
        $this->target = $target;

        $this->dump->file($source);
        new Import($this->dump);

        $this->dump->file($target);
        new Export($this->dump);

        $this->assertFileExists($source);
        $this->assertFileExists($target);

        $files = array($source, $target);

        foreach ($files as &$file) {
            $data = file_get_contents($file);

            if (pathinfo($file, PATHINFO_EXTENSION) === 'gz') {
                $data = gzinflate(substr($data, 10, -8));
            }

            $file = join("\n", array_slice(explode("\n", $data), 1, -2));
        }

        $this->assertEquals($files[0], $files[1]);
    }

    public function provider()
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/fixtures/*[.sql|.gz]';

        if ($files = glob($path, GLOB_NOSORT)) {
            foreach ($files as &$file) {
                $file = array($file, \test_tmp_dir . '/rah_danpu_' . md5(uniqid(rand(), true)) . '_' . basename($file));
            }

            return $files;
        }

        throw new \Exception('Unable to read fixtures.');
    }

    public function setUp()
    {
        $this->dump = new Dump;
        $this->dump
            ->dsn(\test_db_dsn)
            ->user(\test_db_user)
            ->pass(\test_db_pass)
            ->tmp(\test_tmp_dir)
            ->file(dirname(dirname(dirname(__DIR__))) . '/flush.sql');

        new Import($this->dump);
    }

    public function tearDown()
    {
        unlink($this->target);
    }
}
