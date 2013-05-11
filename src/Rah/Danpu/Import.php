<?php

/**
 * Restores a database from a SQL dump file.
 *
 * @example
 * $config = new Rah_Danpu_Dump;
 * $config
 *    ->file('/path/to/target/dump/file.sql')
 *    ->db('database')
 *    ->host('localhost')
 *    ->user('username')
 *    ->pass('password')
 *    ->temp('/tmp');
 *
 * new Rah_Danpu_Import($config);
 */

class Rah_Danpu_Import extends Rah_Danpu_Base
{
    /**
     * Runs the dump file.
     */

    public function init()
    {
        $this->connect();

        if ($this->compress)
        {
            $gzip = new Rah_Danpu_Compress();
            $gzip->unpack($this->filename, $this->temp);
        }
        else
        {
            copy($this->filename, $this->temp);
        }

        $this->open($this->temp, 'r');
        $this->import();
        fclose($this->file);
    }

    /**
     * Processes the SQL file.
     *
     * Reads a SQL file by line by line. Expects that
     * individual queries are separated by semicolons,
     * and that quoted values are properly escaped,
     * including newlines.
     *
     * Queries themselves can not contain any comments.
     * All comments are stripped from the file.
     */

    protected function import()
    {
        $query = '';

        while (!feof($this->file))
        {
            $line = fgets($this->file, 4096);
            $trim = trim($line);

            if ($trim !== '' && strpos($trim, '--') !== 0 && strpos($trim, '/*') !== 0)
            {
                $query .= $line;

                if (substr($trim, -1) === ';')
                {
                    $this->pdo->exec(substr(trim($query), 0, -1));
                    $query = '';
                }
            }
        }
    }
}