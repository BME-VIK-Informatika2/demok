<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/models.php';

use Illuminate\Database\Capsule\Manager as Capsule;

connectDatabase();
$schema = Capsule::schema();
foreach (Capsule::select('SHOW TABLES') as $table) {
    $tableName = array_values((array)$table)[0];
    $schema->drop($tableName);
}

Capsule::schema()->create('termekek', function ($table) {
    $table->id('id');
    $table->string('nev', 50);
    $table->integer('raktarkeszlet')->default(50);
    $table->integer('ar');
});

Capsule::schema()->create('vevok', function ($table) {
    $table->id('id');
    $table->string('nev', 50);
    $table->string('cim', 100)->nullable();
    $table->string('telefon', 20)->nullable();
});

Capsule::schema()->create('megrendelesek', function ($table) {
    $table->id('id');
    $table->foreignId('vevo_id')->references('id')->on('vevok');
    $table->dateTime('datum');
});

Capsule::schema()->create('megrendeles_tetelek', function ($table) {
    $table->foreignId('termek_id')->references('id')->on('termekek');
    $table->foreignId('megrendeles_id')->references('id')->on('megrendelesek');
    $table->primary(['termek_id', 'megrendeles_id']);
    $table->integer('db');
});

Capsule::table('termekek')->insert([
    ['nev' => 'Alma', 'raktarkeszlet' => 150, 'ar' => 400],
    ['nev' => 'Körte', 'raktarkeszlet' => 120, 'ar' => 800],
    ['nev' => 'Eper', 'raktarkeszlet' => 100, 'ar' => 2000],
    ['nev' => 'Szőlő', 'raktarkeszlet' => 110, 'ar' => 1600],
    ['nev' => 'Banán', 'raktarkeszlet' => 200, 'ar' => 700],
    ['nev' => 'Narancs', 'raktarkeszlet' => 220, 'ar' => 800],
    ['nev' => 'Kivi', 'raktarkeszlet' => 0, 'ar' => null],
]);

Capsule::table('vevok')->insert([
    ['nev' => 'Kiss Árpád', 'cim' => '1111 Budapest, Egy utca 3', 'telefon' => null],
    ['nev' => 'Nagy Géza', 'cim' => '5000 Szolnok, Fa tér 4', 'telefon' => '+36 20 987 4562'],
    ['nev' => 'Kovács Ágnes', 'cim' => null, 'telefon' => '+36 70 123 4567'],
    ['nev' => 'Tóth István', 'cim' => '9000 Győr, Tó utca 6', 'telefon' => '+36 30 555 5555'],
    ['nev' => 'Tóth Istvánné', 'cim' => '9000 Győr, Tó utca 6', 'telefon' => '+36 30 555 5555'],
    ['nev' => 'Varga Ferenc', 'cim' => '1115 Budapest, Magyar tudósok krt. 2/Q', 'telefon' => '+36 20 123 4567'],
]);

Capsule::table('megrendelesek')->insert([
    ['vevo_id' => 2, 'datum' => '2023-01-01'],
    ['vevo_id' => 1, 'datum' => '2023-02-02'],
    ['vevo_id' => 1, 'datum' => '2023-03-03'],
    ['vevo_id' => 3, 'datum' => '2022-04-04'],
    ['vevo_id' => 2, 'datum' => '2022-05-05'],
    ['vevo_id' => 2, 'datum' => date('Y-m-d')],
]);

Capsule::table('megrendeles_tetelek')->insert([
    ['termek_id' => 1, 'megrendeles_id' => 1, 'db' => 5],
    ['termek_id' => 2, 'megrendeles_id' => 1, 'db' => 4],
    ['termek_id' => 3, 'megrendeles_id' => 1, 'db' => 6],
    ['termek_id' => 1, 'megrendeles_id' => 2, 'db' => 5],
    ['termek_id' => 4, 'megrendeles_id' => 2, 'db' => 10],
    ['termek_id' => 3, 'megrendeles_id' => 3, 'db' => 2],
    ['termek_id' => 1, 'megrendeles_id' => 3, 'db' => 3],
    ['termek_id' => 2, 'megrendeles_id' => 3, 'db' => 8],
    ['termek_id' => 3, 'megrendeles_id' => 4, 'db' => 1],
    ['termek_id' => 3, 'megrendeles_id' => 5, 'db' => 7],
    ['termek_id' => 4, 'megrendeles_id' => 5, 'db' => 2],
    ['termek_id' => 1, 'megrendeles_id' => 6, 'db' => 4],
]);