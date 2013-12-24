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

    public function testDump($method, $source, $target)
    {
        $this->target = $target;

        if ($method === 'disablekeyschecks') {
            $this->dump
                ->disableAutoCommit(true)
                ->disableUniqueKeyChecks(true)
                ->disableForeignKeyChecks(true);
        }

        $this->dump->file($source);
        $this->assertEquals($source, (string) new Import($this->dump));

        $this->dump->file($target);
        $this->assertEquals($target, (string) new Export($this->dump));

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

    /**
     * @dataProvider provider
     */

    public function testExtending($method, $source, $target)
    {
        $this->dump = new Dump(new Config);
        $this->testDump($method, $source, $target);
    }

    /**
     * @expectedException \Rah\Danpu\Exception
     */

    public function testInvalidConfigPropertySet()
    {
        $dump = new Dump(new Config);
        $dump->invalidProperty('value');
    }

    /**
     * @expectedException \Rah\Danpu\Exception
     */

    public function testInvalidConfigPropertyGet()
    {
        $dump = new Dump(new Config);
        $dump->invalidProperty;
    }

    /**
     * @expectedException \Rah\Danpu\Exception
     */

    public function testInvalidImportSourceFile()
    {
        $dump = new Dump(new Config);
        $dump->file('invalid/invalidFile.sql');
        new Import($dump);
    }

    /**
     * @expectedException \Rah\Danpu\Exception
     */

    public function testInvalidExportTargetFile()
    {
        $dump = new Dump(new Config);
        $dump->file('invalid/invalidFile.sql');
        new Export($dump);
    }

    /**
     * @expectedException \Rah\Danpu\Exception
     */

    public function testInvalidDsn()
    {
        $dump = new Dump(new Config);
        $dump->dsn('invalid');
        new Export($dump);
    }

    public function testIgnoring()
    {
        $this->target = \test_tmp_dir . '/rah_danpu_' . md5(uniqid(rand(), true));
        $expect = __DIR__ . '/../../../fixtures/ignore/triggers_views.sql';

        $this->dump->file(__DIR__ . '/../../../fixtures/default/triggers_views.sql');
        new Import($this->dump);

        $this->dump->file($this->target)->prefix('test_')->ignore(array(
            'user_groups',
            'test_table_2',
            'organization_view',
        ));

        new Export($this->dump);

        $this->assertFileExists($this->target);
        $this->assertFileExists($expect);

        $this->assertEquals(
            join("\n", array_slice(explode("\n", file_get_contents($expect)), 1, -2)),
            join("\n", array_slice(explode("\n", file_get_contents($this->target)), 1, -2))
        );
    }

    public function provider()
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/fixtures/*/*[.sql|.gz]';

        if ($files = glob($path, GLOB_NOSORT)) {
            foreach ($files as &$file) {
                $file = array(
                    basename(dirname($file)),
                    $file,
                    \test_tmp_dir . '/rah_danpu_' . md5(uniqid(rand(), true)) . '_' . basename($file)
                );
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
        if ($this->target) {
            unlink($this->target);
            $this->target = null;
        }
    }

    public static function tearDownAfterClass()
    {
        $dump = new Dump(new Config);
        $dump->file(dirname(dirname(dirname(__DIR__))) . '/flush.sql');
        new Import($dump);
    }
}
