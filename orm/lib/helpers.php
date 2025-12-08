<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

function connectDatabase($host = "localhost", $username = "root", $password = "", $dbname = "webshop_orm"): void
{
    $capsule = new Capsule;

    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => $host,
        'database' => $dbname,
        'username' => $username,
        'password' => $password,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]);

    try {
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    } catch (Exception $e) {
        $error = sprintf('MySQL Hiba : %s', $e->getMessage());
        showErrorPage($error);
    }
}

function showErrorPage($errorMessage, $errorCode = 500): void
{
    http_response_code($errorCode);
    switch ($errorCode) {
        case 404:
            $errorTitle = 'Not Found';
            break;
        case 500:
            $errorTitle = 'Internal Server Error';
            break;
    }
    require './hiba.php';
    exit;
}
