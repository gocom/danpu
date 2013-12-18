<?php

namespace Rah\Danpu\Test;

class Config extends \Rah\Danpu\Config
{
    public $dsn = \test_db_dsn;
    public $user = \test_db_user;
    public $pass = \test_db_pass;
    public $tmp = \test_tmp_dir;
}
