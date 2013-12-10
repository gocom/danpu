<?php

/*
 * Danpu - Database backup library
 * https://github.com/gocom/danpu
 *
 * Copyright (C) 2013 Jukka Svahn
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Rah\Danpu;

/**
 * Creates a SQL dump file from a database.
 *
 * <code>
 * use Rah\Danpu\Dump;
 * use Rah\Danpu\Export;
 * $config = new Dump;
 * $config
 *    ->file('/path/to/target/dump/file.sql')
 *    ->dsn('mysql:dbname=database;host=localhost')
 *    ->user('username')
 *    ->pass('password')
 *    ->tmp('/tmp');
 *
 * new Export($config);
 * </code>
 */

class Export extends Base
{
    /**
     * {@inheritdoc}
     */

    public function init()
    {
        $this->connect();
        $this->tmpFile();
        $this->open($this->temp, 'wb');
        $this->getTables();
        $this->lock();

        try
        {
            $this->dump();
        }
        catch (\Exception $e)
        {
            throw new Exception('Exporting database failed: '.$e->getMessage());
        }

        $this->unlock();
        $this->close();
        $this->move();
    }

    /**
     * Escapes a value for a use in a SQL statement.
     *
     * @param  mixed $value
     * @return string
     */

    protected function escape($value)
    {
        if ($value === null)
        {
            return 'NULL';
        }

        if ((string) intval($value) === $value)
        {
            return (int) $value;
        }

        return $this->pdo->quote($value);
    }

    /**
     * Dumps database contents to a temporary file.
     */

    protected function dump()
    {
        $this->write('-- '.date('c').' - '.$this->config->db.'@'.$this->config->host, false);
        $this->dumpTables();
        $this->dumpViews();
        $this->dumpTriggers();
        $this->write("\n\n-- Completed on: ".date('c'), false);
    }

    /**
     * Dumps tables.
     *
     * @throws Exception
     * @since  2.5.0
     */

    protected function dumpTables()
    {
        $this->tables->execute();

        foreach ($this->tables->fetchAll(\PDO::FETCH_ASSOC) as $a)
        {
            $table = current($a);

            if ((isset($a['Table_type']) && $a['Table_type'] === 'VIEW') || in_array($table, (array) $this->config->ignore, true))
            {
                continue;
            }

            if ((string) $this->config->prefix !== '' && strpos($table, $this->config->prefix) !== 0)
            {
                continue;
            }

            if (($structure = $this->pdo->query('show create table `'.$table.'`')) === false)
            {
                throw new Exception('Unable to get the structure for "'.$table.'"');
            }

            $this->write("\n\n-- Table structure for table `{$table}`\n\n", false);
            $this->write('DROP TABLE IF EXISTS `'.$table.'`');

            foreach ($structure as $row)
            {
                $this->write(end($row));
            }

            if ($this->config->data)
            {
                $this->write("\n\n-- Dumping data for table `{$table}`\n\n", false);
                $this->write("LOCK TABLES `{$table}` WRITE");

                $rows = $this->pdo->prepare('select * from `'.$table.'`');
                $rows->execute();

                while ($a = $rows->fetch(\PDO::FETCH_ASSOC))
                {
                    $this->write("INSERT INTO `{$table}` VALUES (".implode(',', array_map(array($this, 'escape'), $a)).")");
                }

                $this->write('UNLOCK TABLES');
            }
        }
    }

    /**
     * Dumps views.
     *
     * @throws Exception
     * @since  2.5.0
     */

    protected function dumpViews()
    {
        $this->tables->execute();

        foreach ($this->tables->fetchAll(\PDO::FETCH_ASSOC) as $a)
        {
            $view = current($a);

            if (!isset($a['Table_type']) || $a['Table_type'] !== 'VIEW' || in_array($view, (array) $this->config->ignore, true))
            {
                continue;
            }

            if ((string) $this->config->prefix !== '' && strpos($view, $this->config->prefix) !== 0)
            {
                continue;
            }

            if (($structure = $this->pdo->query('show create view `'.$view.'`')) === false)
            {
                throw new Exception('Unable to get the structure for view "'.$view.'"');
            }

            if ($structure = $structure->fetch(\PDO::FETCH_ASSOC))
            {
                $this->write("\n\n-- Structure for view `{$view}`\n\n", false);
                $this->write('DROP VIEW IF EXISTS `'.$view.'`');
                $this->write($structure['Create View']);
            }
        }
    }

    /**
     * Dumps triggers.
     *
     * @since 2.5.0
     */

    protected function dumpTriggers()
    {
        if ($this->config->triggers)
        {
            $triggers = $this->pdo->prepare('show triggers');
            $triggers->execute();

            while ($a = $triggers->fetch(\PDO::FETCH_ASSOC))
            {
                if (in_array($a['Table'], (array) $this->config->ignore, true))
                {
                    continue;
                }

                if ((string) $this->config->prefix !== '' && strpos($a['Table'], $this->config->prefix) !== 0)
                {
                    continue;
                }

                $this->write("\n\n-- Trigger structure `{$a['Trigger']}`\n\n", false);
                $this->write('DROP TRIGGER IF EXISTS `'.$a['Trigger'].'`');
                $this->write("DELIMITER //\nCREATE TRIGGER `{$a['Trigger']}` {$a['Timing']} {$a['Event']} ON `{$a['Table']}` FOR EACH ROW\n{$a['Statement']}\n//\nDELIMITER ;\n", false);
            }
        }
    }
}
