<?php

/**
 * Creates a SQL dump file from a database.
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
 * new Rah_Danpu_Export($config);
 */

class Rah_Danpu_Export extends Rah_Danpu_Base
{
    /**
     * Dumps the database.
     */

    public function init()
    {
        $this->connect();
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

            while ($a = $rows->fetch(PDO::FETCH_ASSOC))
            {
                $this->write("INSERT INTO `{$table}` VALUES (".implode(',', array_map(array($this, 'escape'), $a)).")");
            }

            $this->write('UNLOCK TABLES');
        }
    }
}