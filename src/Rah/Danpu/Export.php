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
 * @example
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
 */

class Export extends Base
{
    /**
     * Dumps the database.
     */

    public function init()
    {
        $this->connect();
        $this->tmpFile();
        $this->open($this->temp, 'wb');
        $this->getTables();
        $this->lock();
        $this->dump();
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
        $this->write('-- '. date('c') . ' - ' . $this->config->db . '@' . $this->config->host, false);

        foreach ($this->tables as $table)
        {
            if (($structure = $this->pdo->query('SHOW CREATE TABLE `'.$table.'`')) === false)
            {
                throw new Exception('Unable to get the structure for "'.$table.'"');
            }

            $this->write("\n\n-- Table structure for table `{$table}`\n\n", false);
            $this->write('DROP TABLE IF EXISTS `'.$table.'`');

            foreach ($structure as $row)
            {
                $this->write(end($row));
            }

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

        $this->write("\n\n-- Completed on: " . date('c'), false);
    }
}