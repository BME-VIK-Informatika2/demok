<?php
require __DIR__ . '/../../app/Application.php';

// Minden esetben JSON választ adunk
header("Content-Type: application/json");

try {
    // Alkalmazás inicializálása
    $app = new Application();

    // GET kérés esetén kiírjuk az állapotot
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = $app->getLedStatus();
        // JSON konvertálás
        echo json_encode([
            'data' => $data
        ]);
        exit(0);
    }

    // POST kérés esetén beállítjuk a kapott állapotot
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $app->setLedStatus($_POST);
        // JSON konvertálás
        echo json_encode([
            'data' => $data
        ]);
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


