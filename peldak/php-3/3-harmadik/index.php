<?php

// Egy konferencián adatbázisban tároljuk a szekciókat és az azokon résztvevőket.
// - Készíts egy oldalt, ahol listázni lehet a szekciókat a résztvevők számával
//   és rendezd a listát népszerűség szerint csökkenő sorrendbe.

// Megadjuk az adatbázis nevét
$dbname = "info2";

try {
    // Kapcsolódás az adatbázishoz, az adatokat statikusan adjuk meg az egyszerűség kedvéért
    $db = new PDO("mysql:host=localhost;port=3306;dbname=$dbname", "root", "");
    // Beállítjuk, hogy a hibákat kivételként kezelje a PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = $db->query("SELECT s.*, COUNT(a.attendee_id) as headcount FROM sessions s
                                LEFT JOIN attendance a ON s.id = a.session_id
                                GROUP BY s.id
                                ORDER BY headcount DESC, s.name ASC
    ");

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
    <title>Konferencia</title>
</head>
<body>
<h1>Szekciók</h1>
<ul>
    <!-- A kapott eredményeket listázzuk, jelen példában egy flexbox konténert készítünk -->
    <?php while ($row = $results->fetch(PDO::FETCH_OBJ)): ?>
        <li><?=$row->name?> (<?=$row->headcount?> fő) - <a href="insert.php?id=<?=$row->id?>">Jelenlét</a></li>
    <?php endwhile; ?>
</ul>
</body>
</html>
