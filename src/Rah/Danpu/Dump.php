<?php

/**
 * Config template and defaults.
 *
 * @example
 * $dump = new Rah_Danpu_Dump();
 * $dump
 *     ->db('database')
 *     ->file('/path/to/dump.sql');
 */

class Rah_Danpu_Dump
{
    /**
     * The database name.
     *
     * @var string
     * @example
     * $dump->db('myDatabase');
     */

    public $db;

    /**
     * The hostname.
     *
     * @var string
     * @example
     * $dump->host('hostname.test');
     */

    public $host = 'localhost';

    /**
     * The username.
     *
     * @var string
     * @example
     * $dump->user('username');
     */

    public $user;

    /**
     * The password.
     *
     * @var string
     * @example
     * $dump->pass('password');
     */

    public $pass = '';

    /**
     * Connection attributes.
     *
     * @var array
     * @example
     * $dump->attributes(array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
     */

    public $attributes = array();

    /**
     * Encoding.
     *
     * @var string
     * @example
     * $dump->encoding('latin1');
     */

    public $encoding = 'utf8';

    /**
     * Temporary directory.
     *
     * @var string
     * @example
     * $dump->tmp('/path/to/temp/dir');
     */

    public $tmp = '/tmp';

    /**
     * The target SQL dump file.
     *
     * To enable Gzipping, add '.gz' extension
     * to the filename.
     *
     * @var string
     * @example
     * $dump->file('/path/to/dump.sql');
     */

    public $file;

    /**
     * Constructor.
     */

    public function __construct()
    {
        $this->attributes = array(
            PDO::ATTR_ORACLE_NULLS             => PDO::NULL_NATURAL,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
        );
    }

    /**
     * Sets database connection details.
     *
     * @return Rah_Danpu_Dump
     */

    public function __call($name, $args)
    {
        $this->$name = $args[0];
        return $this;
    }
}