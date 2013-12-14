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
 * Configures a dump instance.
 *
 * <code>
 * use Rah\Danpu\Dump;
 * $dump = new Dump();
 * $dump
 *     ->dsn('mysql:dbname=database;host=localhost')
 *     ->file('/path/to/dump.sql');
 * </code>
 */

class Dump extends Config
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        $this->attributes = array(
            \PDO::ATTR_ORACLE_NULLS             => \PDO::NULL_NATURAL,
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
            \PDO::ATTR_ERRMODE                  => \PDO::ERRMODE_EXCEPTION,
        );
    }

    /**
     * Sets a configuration property.
     *
     * <code>
     * $dump = new \Rah\Danpu\Dump();
     * $dump->name('value');
     * </code>
     *
     * @param  string $name Method
     * @param  array  $args Arguments
     * @return Dump
     * @throws Exception
     */

    public function __call($name, $args)
    {
        if (property_exists($this, $name) === false)
        {
            throw new Exception('Unknown config option given: '.$name);
        }

        $this->$name = $args[0];
        return $this;
    }
}
