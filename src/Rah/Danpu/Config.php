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
 * Dump config.
 *
 * @since 2.3.0
 * @example
 * class myConfig Extends \Rah\Danpu\Config
 * {
 *  $this->dsn = 'mysql:dbname=database;host=localhost';
 *  $this->file = '/path/to/dump.sql';
 * }
 */

abstract class Config
{
    /**
     * Data source name.
     *
     * @var   string
     * @since 2.2.0
     */

    public $dsn;

    /**
     * The database name.
     *
     * @var        string
     * @deprecated 2.2.0
     */

    public $db;

    /**
     * The hostname.
     *
     * @var        string
     * @deprecated 2.2.0
     */

    public $host = 'localhost';

    /**
     * The username.
     *
     * @var string
     */

    public $user;

    /**
     * The password.
     *
     * @var string
     */

    public $pass = '';

    /**
     * Connection attributes.
     *
     * @var array
     */

    public $attributes = array();

    /**
     * Encoding.
     *
     * @var string
     */

    public $encoding = 'utf8';

    /**
     * An array of ignored tables, views and triggers based on the target table.
     *
     * This can be used to exclude confidential or temporary
     * data from the backup.
     *
     * @var   string
     * @since 2.1.0
     */

    public $ignore = array();

    /**
     * Temporary directory.
     *
     * @var string
     */

    public $tmp = '/tmp';

    /**
     * The target SQL dump file.
     *
     * To enable Gzipping, add '.gz' extension
     * to the filename.
     *
     * @var string
     */

    public $file;

    /**
     * Dump table data.
     *
     * @var   bool
     * @since 2.4.0
     */

    public $data = true;

    /**
     * Dump triggers.
     *
     * @var   bool
     * @since 2.5.0
     */

    public $triggers = true;

    /**
     * Table prefix.
     *
     * @var   string
     * @since 2.6.0
     */

    public $prefix = '';
}