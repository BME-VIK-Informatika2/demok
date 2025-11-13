<?php

// Egy adatgyűjtő rendszerben adatbázisban tároljuk szenzorok megfigyeléseit.
// - Listázd ki a szenzorokat, a megfigyelések szélsőértékeit és átlagukat.

// Megadjuk az adatbázis nevét
$dbname = "info2";

try {
    // Kapcsolódás az adatbázishoz, az adatokat statikusan adjuk meg az egyszerűség kedvéért
    $db = new PDO("mysql:host=localhost;port=3306;dbname=$dbname", "root", "");
    // Beállítjuk, hogy a hibákat kivételként kezelje a PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Adatok lekérdezésére összeállítjuk az SQL lekérdezést, a főbb logikát itt adjuk meg
    $select = "
        SELECT s.*, MIN(o.value) AS 'min', MAX(o.value) AS 'max', AVG(o.value) AS 'avg' 
        FROM sensors s
        LEFT JOIN observations o ON s.id = o.sensor_id
        GROUP BY s.id
    ";
    // Mivel nincs paraméter, így sima query-t használunk
    $results = $db->query($select);

} catch (PDOException $e) {
    // Hiba esetén írjuk ki az üzenetet és lépjünk ki
    die('Hiba: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Szenzorok</title>
</head>
<body>
<table border="1" cellspacing="0">
    <tr>
        <th>Szenzor</th>
        <th>Minimum</th>
        <th>Maximum</th>
        <th>Átlag</th>
        <th></th>
    </tr>
    <!-- A $result változóból a fetch segítségével olvassuk ki a sorokat egy asszociatív tömbbe -->
    <?php while ($row = $results->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <!-- A tömbből kiolvassuk az oszlopokat, ha null az érték, akkor 0-át írunk ki -->
            <td><?= $row['name'] ?? 0 ?></td>
            <td><?= $row['min'] ?? 0 ?></td>
            <td><?= $row['max'] ?? 0 ?></td>
            <td><?= $row['avg'] ?? 0 ?></td>
            <!-- Minden sor végén megjelenítünk egy linket, amivel gyorsan tudunk hozzáadni megfigyelést -->
            <!-- A link átirányít az insert.php oldalra és átadjuk URL paraméterben a szenzor azonosítóját -->
            <td><a href="insert.php?id=<?= $row['id'] ?>">Megfigyelés hozzáadása</a></td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
