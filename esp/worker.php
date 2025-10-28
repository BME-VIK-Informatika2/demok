<?php

require __DIR__ . '/app/Application.php';

try {
    // Alkalmazás inicializálása
    $app = new Application();
    $app->initDatabase();

    // Végtelen ciklus az adatok beszúrására
    while (true) {
        try {
            // Várunk 1 másodpercet
            sleep(1);

            // Lekérjük a frissítjük a sensor értékét
            $value = $app->updatePhotoresistorStatus();
            echo "Új érték beszúrása az adatbázisba: $value" . PHP_EOL;
        } catch (Exception $e) {
            // Hiba kiírása, de ismételt próbálkozás (ESP hiba)
            echo $e->getMessage() . PHP_EOL;
            continue;
        }
    }
} catch (Exception $e) {
    // Hiba kiírása, és kilépés a programból (DB hiba)
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
