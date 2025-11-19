<?php

require_once 'vendor/autoload.php';
require_once 'SimpleLogger.php';

use Symfony\Component\VarDumper\VarDumper;
use Dotenv\Dotenv;

// .env betöltése, ha létezik
if(file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Logger inicializálása
$log_file = $_ENV['LOG_FILE'] ?? 'app.log';
$log = new SimpleLogger(__DIR__ . '/' . $log_file);

// Debug üzenet a környezeti változókról
$log->debug('Környezeti változók betöltve: ', $_ENV);

// Minta adat
$subject = [
    'name' => 'Info2',
    'code' => 'VIAUAC10',
    'credits' => 5
];

// Logolás
$log->info('Tárgy adatok betöltve:', $subject);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
</head>
<body>
    <h1>Beépített <tt>var_dump(...)</tt></h1>
    <?php var_dump($subject); ?>
    <h1>Szebb kiíratás a <tt>symfony/var-dumper</tt> csomagból</h1>
    <?php VarDumper::dump($subject); ?>
</body>
</html>
