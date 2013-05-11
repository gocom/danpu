<?php

/**
 * Simple SQL dump utility written in PHP.
 */

abstract class Rah_Danpu_Base
{
    /**
     * The config.
     *
     * @var Rah_Danpu_Dump
     */

    protected $config;

    /**
     * An array of table in the database.
     *
     * @var array
     */

    protected $tables = array();

    /**
     * Ignored tables.
     *
     * @var array
     */

    protected $ignored = array();

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
     * @param string $filename  The SQL dump file
     * @param Rah_Danpu_Dump The config
     */

    public function __construct($config)
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
        try
        {
            $this->pdo = new PDO("mysql:dbname={$this->config->db};host={$this->config->host}", $this->config->user, $this->config->pass);
            $this->pdo->exec('SET NAMES '.$this->config->encoding);

            foreach ($this->config->attributes as $name => $value)
            {
                $this->pdo->setAttribute($name, $value);
            }
        }
        catch (PDOException $e)
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
                if (!in_array($table, $this->ignored))
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
     * @param string $flags
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
     * Writes a SQL statement to the file.
     *
     * @param string $string  The string to write
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
     * Moves temporary file to the final location.
     */

    protected function move()
    {
        if ($this->compress)
        {
            $gzip = new Rah_Danpu_Compress();
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
}