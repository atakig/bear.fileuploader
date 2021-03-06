<?php

namespace FileUpload;

$id = isset($_SERVER['BEAR_DB_ID']) ? $_SERVER['BEAR_DB_ID'] : 'root';
$password = isset($_SERVER['BEAR_DB_PASSWORD']) ? $_SERVER['BEAR_DB_PASSWORD'] : '';

$slaveId = isset($_SERVER['BEAR_DB_ID_SLAVE']) ? $_SERVER['BEAR_DB_ID_SLAVE'] : 'root';
$slavePassword = isset($_SERVER['BEAR_DB_PASSWORD_SLAVE']) ? $_SERVER['BEAR_DB_PASSWORD_SLAVE'] : '';

$appDir = dirname(__DIR__);

$sqlite_path = dirname(__FILE__) . '/../data/uploadFiles.sqlite';
// @Named($key) => instance
$config = [
    // database
    'master_db' => [
        'driver' => 'pdo_sqlite',
        'path' => $sqlite_path
//        'host' => 'localhost',
//        'dbname' => '_DB_NAME_',
//        'user' => $id,
//        'password' => $password,
//        'charset' => 'UTF8'

    ],
    'slave_db' => [
        'driver' => 'pdo_sqlite',
        'path' => $sqlite_path
//        'host' => 'localhost',
//        'dbname' => '_DB_NAME_',
//        'user' => $slaveId,
//        'password' => $slavePassword,
//        'charset' => 'UTF8'
    ],
    // constants
    'app_name' => __NAMESPACE__,
    'app_dir' => $appDir,
    'tmp_dir' => $appDir . '/data/tmp',
    'log_dir' => $appDir . '/data/log'
];

return $config;
