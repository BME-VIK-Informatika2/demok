<?php

// --- Feladat: ------------------
// Egy mérési adatgyűjtő rendszerben a heti hőmérsékleti adatokat egyetlen stringben tároljuk, vesszővel elválasztva.
// Írj egy PHP függvényt, amely ezt a stringet feldolgozza, és kiírja következő információkat.
// - Mennyi volt az átlagos hőmérséklet a héten?
// - Melyik nap volt a legmelegebb?
// - Hány nap volt a hőmérséklet 20 fok felett?
// -------------------------------

// Ez a függvény fogja feldolgozni a hőmérsékleti adatokat, a típus megadások opcionálisak (bemenet string, kimenet
// void, vagyis nincs)
function processTemperatures(string $tempString): void
{
    // A stringet tömbbé alakítjuk a vesszők mentén az explode utasítással
    $tempsArray = explode(",", $tempString);

    // Mennyi volt az átlagos hőmérséklet a héten?
    // Kiszámoljuk az átlagot az összes hőmérséklet összeadásával és a napok számával való osztással
    $averageTemp = array_sum($tempsArray) / count($tempsArray);
    // Kiírjuk az eredményt, kerekítve két tizedesjegyre, a PHP_EOL a sortörést jelenti
    echo "Átlagos hőmérséklet a héten: " . round($averageTemp, 2) . "°C" . PHP_EOL;

    // Melyik nap volt a legmelegebb?
    // Kiszámoljuk a maximum hőmérsékletet a max függvénnyel
    $maxTemp = max($tempsArray);
    // Készítünk egy tömböt a napok neveivel, ahol az indexek megfelelnek a hőmérsékleti adatok indexeinek
    $days = ['hetfo', 'kedd', 'szerda', 'csutortok', 'pentek', 'szombat', 'vasarnap'];
    // Végigmegyünk a hőmérsékleti adatokon, a kulcs (vagyis 0, 1, 2, ...) kerül a $day változóba, az érték
    // pedig a $temp változóba
    foreach ($tempsArray as $day => $temp) {
        // Vizsgáljuk, hogy a $temp változó értéke megegyezik a maximum hőmérséklettel
        if ($temp == $maxTemp) {
            // Ha igen, kiírjuk a nap nevét és a hőmérsékletet, majd kilépünk a ciklusból a break utasítással.
            echo "Legmelegebb nap: " . $days[$day] . " (" . $maxTemp . "°C)" . PHP_EOL;
            break;
        }
    }

    // Hány nap volt a hőmérséklet 20 fok felett?
    // Inicializálunk egy számlálót 0-ra
    $counter = 0;
    // Készítünk egy ciklust amivel végig tudunk menni a hőmérsékleti adatokon
    for ($i = 0; $i < count($tempsArray); $i++) {
        // Ha a hőmérséklet nagyobb vagy egyenlő 20 fokkal, növeljük a számlálót eggyel
        if ($tempsArray[$i] >= 20) {
            $counter++;
        }
    }
    // Kiírjuk a számláló értékét, ebben az esetben string interpolációt használunk
    echo "Hőmérséklet 20 fok felett volt: $counter nap\n";
}

// Teszteljük a függvényt egy példa hőmérsékleti adatsorral
$temps = "18,22,19,24,21,23,20";
processTemperatures($temps);