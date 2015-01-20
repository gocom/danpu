<?php

/**
 * Danpu - Database backup library
 *
 * @author  Jukka Svahn
 * @license MIT
 * @link    https://github.com/gocom/danpu
 */

/*
 * Copyright (C) 2014 Jukka Svahn
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
        $this->dump();
        $this->unlock();
        $this->close();
        $this->move();
    }

    /**
     * Escapes a value for a use in a SQL statement.
     *
     * @param  mixed &$value
     * @param  mixed $key
     * @param  mixed[] $tableCols
     * @param  boolean $return
     */

    protected function escape(&$value, $key = false, $tableCols = [], $return = false)
    {
        if ($value === null) {
            $value = 'NULL';
        }
        else {
            switch ($tableCols[$key]) {
                case 'BIT':
                case 'TINYINT':
                case 'SMALLINT':
                case 'MEDIUMINT':
                case 'INT':
                case 'INTEGER':
                case 'BIGINT':
                    $value = (int)$value;
                    break;
                case 'REAL':
                case 'DOUBLE':
                case 'FLOAT':
                case 'DECIMAL':
                case 'NUMERIC':
                    $value = (float)$value;
                    break;
                case 'DATE':
                case 'TIME':
                case 'TIMESTAMP':
                case 'DATETIME':
                case 'YEAR':
                    $value = $this->pdo->quote($value);
                    break;
                case 'BINARY':
                case 'VARBINARY':
                case 'TINYBLOB':
                case 'BLOB':
                case 'MEDIUMBLOB':
                case 'LONGBLOB':
                    $value = $this->pdo->quote($value);
                    break;
                case 'CHAR':
                case 'VARCHAR':
                case 'TINYTEXT':
                case 'TEXT':
                case 'MEDIUMTEXT':
                case 'LONGTEXT':
                case 'ENUM':
                case 'SET':
                    $value = $this->pdo->quote($value);
                    break;
                default:
                    $value = $this->pdo->quote($value);
                    break;
            }
        }

        /**
         * If method don't recognize datatype, intouched value stay in $value
         */
        if($return)
        {
            return $value;
        }
        return;
    }

    /**
     * Dumps database contents to a temporary file.
     */

    protected function dump()
    {
        $this->write('-- '.date('c').' - '.$this->config->dsn, false);

        if ($this->config->disableAutoCommit === true) {
            $this->write('SET AUTOCOMMIT = 0');
        }

        if ($this->config->disableForeignKeyChecks === true) {
            $this->write('SET FOREIGN_KEY_CHECKS = 0');
        }

        if ($this->config->disableUniqueKeyChecks === true) {
            $this->write('SET UNIQUE_CHECKS = 0');
        }

        if ($this->config->createDatabase === true) {
            $this->write(
                'CREATE DATABASE IF NOT EXISTS `'.$this->database.'` '.
                'DEFAULT CHARACTER SET = '.$this->escape($this->config->encoding, false, [], true)
            );
            $this->write('USE `'.$this->database.'`');
        }

        $this->dumpTables();
        $this->dumpViews();
        $this->dumpTriggers();
        $this->dumpEvents();

        if ($this->config->disableForeignKeyChecks === true) {
            $this->write('SET FOREIGN_KEY_CHECKS = 1');
        }

        if ($this->config->disableUniqueKeyChecks === true) {
            $this->write('SET UNIQUE_CHECKS = 1');
        }

        if ($this->config->disableAutoCommit === true) {
            $this->write('COMMIT');
            $this->write('SET AUTOCOMMIT = 1');
        }

        $this->write("\n-- Completed on: ".date('c'), false);
    }

    /**
     * Dumps tables.
     *
     * @since  2.5.0
     */

    protected function dumpTables()
    {
        $this->tables->execute();

        foreach ($this->tables->fetchAll(\PDO::FETCH_ASSOC) as $a) {
            $table = current($a);
            $tableCols = [];

            if (isset($a['Table_type']) && $a['Table_type'] === 'VIEW') {
                continue;
            }

            if (in_array($table, (array) $this->config->ignore, true)) {
                continue;
            }

            if ((string) $this->config->prefix !== '' && strpos($table, $this->config->prefix) !== 0) {
                continue;
            }

            $structure = $this->pdo->query('SHOW CREATE TABLE `'.$table.'`')->fetch(\PDO::FETCH_ASSOC);

            $this->write("\n-- Table structure for table `{$table}`\n", false);
            $this->write('DROP TABLE IF EXISTS `'.$table.'`');
            $createStatement = end($structure);
            $this->write($createStatement);

            preg_match_all('/^((.*) ((?|BIT|TINYINT|SMALLINT|MEDIUMINT|INT|INTEGER|BIGINT|REAL|DOUBLE|FLOAT|DECIMAL|NUMERIC|DATE|TIME|TIMESTAMP|DATETIME|YEAR|CHAR|VARCHAR|BINARY|VARBINARY|TINYBLOB|BLOB|MEDIUMBLOB|LONGBLOB|TINYTEXT|TEXT|MEDIUMTEXT|LONGTEXT|ENUM|SET)).* (.*),)$/miU', $createStatement, $matches);

            if(isset($matches[2]) && isset($matches[3]))
            {
                foreach($matches[2] as $key => $rowName)
                {
                    $rowName = trim($rowName, ' `');
                    if(isset($matches[3][$key]))
                    {
                        $tableCols[$rowName] = strtoupper(trim($matches[3][$key]));
                    }
                }
            }

            if ($this->config->data === true) {
                $this->write("\n-- Dumping data for table `{$table}`\n", false);
                $this->write("LOCK TABLES `{$table}` WRITE");

                $rows = $this->pdo->prepare('SELECT * FROM `'.$table.'`');
                $rows->execute();

                while ($a = $rows->fetch(\PDO::FETCH_ASSOC)) {
                    array_walk($a, [$this, 'escape'], $tableCols);
                    $this->write("INSERT INTO `{$table}` VALUES (".implode(',', $a).")");
                }

                $this->write('UNLOCK TABLES');
            }
        }
    }

    /**
     * Dumps views.
     *
     * @since  2.5.0
     */

    protected function dumpViews()
    {
        $this->tables->execute();

        foreach ($this->tables->fetchAll(\PDO::FETCH_ASSOC) as $a) {
            $view = current($a);

            if (!isset($a['Table_type']) || $a['Table_type'] !== 'VIEW') {
                continue;
            }

            if (in_array($view, (array) $this->config->ignore, true)) {
                continue;
            }

            if ((string) $this->config->prefix !== '' && strpos($view, $this->config->prefix) !== 0) {
                continue;
            }

            $structure = $this->pdo->query('SHOW CREATE VIEW `'.$view.'`');

            if ($structure = $structure->fetch(\PDO::FETCH_ASSOC)) {
                if (isset($structure['Create View'])) {
                    $this->write("\n-- Structure for view `{$view}`\n", false);
                    $this->write('DROP VIEW IF EXISTS `'.$view.'`');
                    $this->write($structure['Create View']);
                }
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
        if ($this->config->triggers === true && version_compare($this->version, '5.0.10') >= 0) {
            $triggers = $this->pdo->prepare('SHOW TRIGGERS');
            $triggers->execute();

            while ($a = $triggers->fetch(\PDO::FETCH_ASSOC)) {
                if (in_array($a['Table'], (array) $this->config->ignore, true)) {
                    continue;
                }

                if ((string) $this->config->prefix !== '' && strpos($a['Table'], $this->config->prefix) !== 0) {
                    continue;
                }

                $this->write("\n-- Trigger structure `{$a['Trigger']}`\n", false);
                $this->write('DROP TRIGGER IF EXISTS `'.$a['Trigger'].'`');

                $query = "CREATE TRIGGER `{$a['Trigger']}`".
                    " {$a['Timing']} {$a['Event']} ON `{$a['Table']}`".
                    " FOR EACH ROW\n{$a['Statement']}";

                $delimiter = $this->getDelimiter('//', $query);
                $this->write("DELIMITER {$delimiter}\n{$query}\n{$delimiter}\nDELIMITER ;", false);
            }
        }
    }

    /**
     * Dumps events.
     *
     * @since 2.7.0
     */

    protected function dumpEvents()
    {
        if ($this->config->events === true && version_compare($this->version, '5.1.12') >= 0) {
            $events = $this->pdo->prepare('SHOW EVENTS');
            $events->execute();

            foreach ($events->fetchAll(\PDO::FETCH_ASSOC) as $a) {
                $event = $a['Name'];

                if (in_array($event, (array) $this->config->ignore, true)) {
                    continue;
                }

                if ((string) $this->config->prefix !== '' && strpos($event, $this->config->prefix) !== 0) {
                    continue;
                }

                $structure = $this->pdo->query('SHOW CREATE EVENT `'.$event.'`');

                if ($structure = $structure->fetch(\PDO::FETCH_ASSOC)) {
                    if (isset($structure['Create Event'])) {
                        $query = $structure['Create Event'];
                        $delimiter = $this->getDelimiter('//', $query);
                        $this->write("\n-- Structure for event `{$event}`\n", false);
                        $this->write('DROP EVENT IF EXISTS `'.$event.'`');
                        $this->write("DELIMITER {$delimiter}\n{$query}\n{$delimiter}\nDELIMITER ;", false);
                    }
                }
            }
        }
    }
}
