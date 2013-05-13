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

/**
 * Config template and defaults.
 *
 * @example
 * $dump = new Rah_Danpu_Dump();
 * $dump
 *     ->dsn('mysql:dbname=database;host=localhost')
 *     ->file('/path/to/dump.sql');
 */

class Rah_Danpu_Dump
{
    /**
     * Data source name.
     *
     * @var string
     * @since 1.2.0
     * @example
     * $dump->dsn('mysql:dbname=database;host=localhost')
     */

    public $dsn;

    /**
     * The database name.
     *
     * @var string
     * @deprecated 1.2.0
     * @example
     * $dump->db('myDatabase');
     */

    public $db;

    /**
     * The hostname.
     *
     * @var string
     * @deprecated 1.2.0
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
     * An array of ignored tables.
     *
     * This can be used to exclude confidential or temporary
     * data from the backup.
     *
     * @var string
     * @example
     * $dump->ignore(array('access_tokens', 'sync_keys'));
     */

    public $ignore = array();

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