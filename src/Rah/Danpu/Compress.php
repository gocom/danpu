<?php

/**
 * Creates a gzip file from the backup.
 *
 * @example
 */

class Rah_Danpu_Compress
{
    /**
     * Compresses files.
     *
     * @param string $from
     * @param string $to
     */

    public function pack($from, $to)
    {
        if (($gzip = gzopen($to, 'wb')) === false)
        {
            throw new Exception('Unable create compressed file.');
        }

        if (($source = fopen($from, 'rb')) === false)
        {
            throw new Exception('Unable open the compression source file.');
        }

        while (!feof($source))
        {
            $content = fread($source, 4096);
            gzwrite($gzip, $content, strlen($content));
        }

        gzclose($gzip);
        fclose($source);
    }

    /**
     * Uncompresses a file.
     *
     * @param string $from
     * @param string $to
     */

    public function unpack($from, $to)
    {
        if (($gzip = gzopen($from, 'rb')) === false)
        {
            throw new Exception('Unable to read compressed file.');
        }

        if (($target = fopen($to, 'w')) === false)
        {
            throw new Exception('Unable to open the target.');
        }

        while ($string = gzread($zip, 4096))
        {
            fwrite($target, $string, strlen($string));
        }

        gzclose($gzip);
        fclose($target);
    }
}