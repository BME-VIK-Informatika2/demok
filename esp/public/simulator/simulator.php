<?php
require __DIR__ . '/../../app/Simulator.php';

// Minden esetben JSON választ adunk
header("Content-Type: application/json");

try {
    // Alkalmazás inicializálása
    $sim = new Simulator();

    // GET kérés esetén kiírjuk az állapotot
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Kiolvassuk az útvonalat q paraméterből (.htaccess segítségével)
        $path = $_GET['q'];

        // Lekérjük a megfelelő állapotot az útvonal alapján
        if($path == 'status')
            // UI API endpoint
            $data = $sim->getStatus();
        elseif ($path == "switch/on-board_led")
            $data = $sim->getLedStatus();
        elseif ($path == "light/on-board_rgb")
            $data = $sim->getRGBStatus();
        elseif ($path == "sensor/photoresistor")
            $data = $sim->getSensorStatus();
        else
            throw new Exception("Ismeretlen útvonal");

        // JSON konvertálás
        echo json_encode($data);
        exit(0);
    }

    // POST kérés esetén beállítjuk a kapott állapotot
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Kiolvassuk az útvonalat q paraméterből (.htaccess segítségével)
        $path = $_GET['q'];

        // Beállítjuk a megfelelő állapotot az útvonal alapján
        if($path == 'status') {
            // UI API endpoint
            $data = $sim->setSensorStatus($_GET['value'] ?? null);
        }
        elseif ($path == "switch/on-board_led/turn_on")
            $data = $sim->setLedStatus("on");
        elseif ($path == "switch/on-board_led/turn_off")
            $data = $sim->setLedStatus("off");
        elseif ($path == "light/on-board_rgb/turn_on")
            $data = $sim->setRGBStatus("on", $_GET['r'] ?? null, $_GET['g'] ?? null, $_GET['b'] ?? null);
        elseif ($path == "light/on-board_rgb/turn_off")
            $data = $sim->setRGBStatus("of", $_GET['r'] ?? null, $_GET['g'] ?? null, $_GET['b'] ?? null);
        else
            throw new Exception("Ismeretlen útvonal");

        // JSON konvertálás
        echo json_encode($data);
        exit(0);
    }

    // Minden egyéb esetben hibát dobunk
    throw new Exception("Nem támogatott HTTP kérés");

} catch (Exception $e) {
    // Hiba esetén beállítjuk a státuszkódot és kiírjuk a hibát
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
    exit(1);
}


