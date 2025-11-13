<?php

function connectDatabase($host = "localhost", $username = "root", $password = "", $dbname = "webshop"): PDO
{
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
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
