<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public $defaultGroup = 'default';
    public $default = [
        'DSN'      => 'pgsql:host=localhost;port=5432;dbname=digithera_local;user=postgres;password=postgres',
        'hostname' => 'localhost',
        'username' => 'postgres',
        'password' => 'postgres',
        'database' => 'digithera_local',
        'DBDriver' => 'Postgre',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 5432,
    ];
}