<?php

// Egy adatgyűjtő rendszerben adatbázisban tároljuk szenzorok megfigyeléseit.
// - Készíts egy inicializáló scriptet, ami elkészíti az adatbázist és feltölti 3 db tetszőleges szenzorral

// Megadjuk az adatbázis nevét
$dbname = "info2";

try {
    // Kapcsolódás az adatbázishoz, az adatokat statikusan adjuk meg az egyszerűség kedvéért
    $db = new PDO("mysql:host=localhost;port=3306;dbname=$dbname", "root", "");
    // Beállítjuk, hogy a hibákat kivételként kezelje a PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tábla létrehozása szenzoroknak, nincs se visszatérési értéke, se paramétere, ezért exec-et használunk
    $db->exec("
        CREATE TABLE IF NOT EXISTS sensors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    // Tábla létrehozása megfigyeléseknek, nincs se visszatérési értéke, se paramétere, ezért exec-et használunk
    $db->exec("
        CREATE TABLE IF NOT EXISTS observations (
            sensor_id INT NOT NULL,
            value FLOAT NOT NULL,
            FOREIGN KEY (sensor_id) REFERENCES sensors(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    // A szenzorok neveit egy tömbben tároljuk
    $sensors = ['Hőmérséklet', 'Páratartalom', 'Nyomás'];
    // Előkészítjük az insert utasítást, prepared statement-et használunk a biztonság érdekében, mivel van paraméter
    // Megjegyzés: Mivel a paraméter nem felhasználói bemenet, így itt nem feltétlenül szükséges a használata,
    // de jó gyakorlatként alkalmazzuk, illetve mivel több azonos sort szúrunk be, így hatékonyabb is.
    $insert = "INSERT INTO sensors (name) VALUES (:name)";
    $statement = $db->prepare($insert);
    // Végigmegyünk a szenzor neveken
    foreach ($sensors as $sensorName) {
        // Végrehajtjuk az előkészített utasítást a megfelelő paraméterrel
        $statement->execute(['name' => $sensorName]);
    }
    // Bezárjuk a kurzort, ezzel felszabadítjuk az erőforrásokat
    $statement->closeCursor();

} catch (PDOException $e) {
    // Hiba esetén írjuk ki az üzenetet és lépjünk ki
    die('Hiba: ' . $e->getMessage());
}
