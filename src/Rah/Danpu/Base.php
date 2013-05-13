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
 * The base class.
 */

abstract class Base
{
    /**
     * The config.
     *
     * @var Rah\Danpu\Dump
     */

    protected $config;

    /**
     * An array of table in the database.
     *
     * @var array
     */

    protected $tables = array();

    /**
     * File pointer.
     *
     * @var resource
     */

    protected $file;

    /**
     * The filename.
     *
     * @var string
     */

    protected $filename;

    /**
     * Path to the temporary file.
     *
     * @var string
     */

    protected $temp;

    /**
     * Compress the dump file.
     */

    protected $compress = false;

    /**
     * Constructor.
     *
     * @param Rah\Danpu\Dump The config
     */

    public function __construct(Dump $config)
    {
        $this->config = $config;
        $this->filename = $this->config->file;
        $this->temp = tempnam($this->config->tmp . '/' . $this->temp, 'Rah_Danpu_');
        unlink($this->temp);
        $this->compress = pathinfo($this->filename, PATHINFO_EXTENSION) === 'gz';
        $this->init();
    }

    /**
     * Destructor.
     */

    public function __destruct()
    {
        $this->close();

        if (file_exists($this->temp))
        {
            unlink($this->temp);
        }

        $this->unlock();
    }

    /**
     * Connects to the database.
     */

    public function connect()
    {
        if ($this->config->dsn)
        {
            $dsn = $this->config->dsn;
        }
        else
        {
            $dsn = "mysql:dbname={$this->config->db};host={$this->config->host}";
        }

        try
        {
            $this->pdo = new \PDO(
                $dsn,
                $this->config->user,
                $this->config->pass
            );

            $this->pdo->exec('SET NAMES '.$this->config->encoding);

            foreach ($this->config->attributes as $name => $value)
            {
                $this->pdo->setAttribute($name, $value);
            }
        }
        catch (\PDOException $e)
        {
            throw new Exception('Connecting to database failed with message: '.$e->getMessage());
        }
    }

    /**
     * Gets an array of tables.
     *
     * @return array|bool
     */

    protected function getTables()
    {
        if ($tables = $this->pdo->query('SHOW TABLES'))
        {
            foreach ($tables as $table)
            {
                if (!in_array($table, $this->config->ignore, true))
                {
                    $this->tables[] = current($table);
                }
            }

            return $this->tables;
        }

        return false;
    }

    /**
     * Locks all tables.
     *
     * @return bool
     */

    protected function lock()
    {
        return $this->pdo->exec('LOCK TABLES `' . implode('` WRITE, `', $this->tables).'` WRITE');
    }

    /**
     * Unlocks all tables.
     *
     * @return bool
     */

    protected function unlock()
    {
        return $this->pdo->exec('UNLOCK TABLES');
    }

    /**
     * Opens a file for writing.
     *
     * @param string $filename The filename
     * @param string $flags    Flags
     */

    protected function open($filename, $flags)
    {
        if (($this->file = fopen($filename, $flags)) === false)
        {
            throw new Exception('Unable to open the target file.');
        }
    }

    /**
     * Closes a file pointer.
     */

    protected function close()
    {
        if (is_resource($this->file))
        {
            fclose($this->file);
        }
    }

    /**
     * Writes a line to the file.
     *
     * @param string $string  The string to write
     * @param bool   $format  Format the string
     */

    protected function write($string, $format = true)
    {
        if ($format)
        {
            $string .= ";\n";
        }

        if (fwrite($this->file, $string, strlen($string)) === false)
        {
            throw new Exception('Unable to write '.strlen($string).' bytes to the dumpfile.');
        }
    }

    /**
     * Moves a temporary file to the final location.
     */

    protected function move()
    {
        if ($this->compress)
        {
            $gzip = new Compress();
            $gzip->pack($this->temp, $this->filename);
            unlink($this->temp);
            return true;
        }

        if (@rename($this->temp, $this->filename))
        {
            return true;
        }

        if (@copy($this->temp, $this->filename) && unlink($this->temp))
        {
            return true;
        }

        throw new Exception('Unable to move the temporary file.');
    }

    /**
     * Returns a path to the target file.
     *
     * @return string
     */

    public function __toString()
    {
        return (string) $this->filename;
    }
}