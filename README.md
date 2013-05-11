Danpu - MySQL dump tool for PHP
=========

Danpu is a dependency-free, cross-platform, portable PHP library for backing up MySQL databases. It has no hard dependencies, and is fit for use in restricted, shared-hosting environments. It requires nothing more than access to your database, PDO and a directory it can write the backup to. The script is optimized and has low memory-footprint, allowing it to handle even larger databases.

Installing
---------

Using [Composer](http://getcomposer.org):

    $ composer.phar require rah/danpu

Usage
---------

To create a new backup, create a new ```Dump``` instance containing your configuration options and feed it to ```Export``` class. The library is PHP 5.2.0 compatible, and uses PSR-0 compatible PEAR-style naming convention.

```php
$dump = new Rah_Danpu_Dump;
$dump
    ->file('/path/to/target/dump/file.sql.gz')
    ->db('database')
    ->host('localhost')
    ->user('username')
    ->pass('password')
    ->temp('/tmp');

new Rah_Danpu_Export($config);
```

The classes throw exceptions on failures.