<?php

function config($key, $default = null){
    $env_path = __DIR__ . "/../.env";
    if(!file_exists($env_path)){
        return $default;
    }
    $config = parse_ini_file(__DIR__ . "/../.env");
    return $config[$key] ?? $default;
}

function connectDatabase(): PDO
{
    $host = config('DB_HOST', 'localhost');
    $dbname = config('DB_NAME', 'blog');
    $username = config('DB_USER', 'root');
    $password = config('DB_PASS', '');

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die('MySQL Error : '. $e->getMessage());
    }
}