<?php

// Egy könyvesbolt saját rendszerében adatbázisban tároljuk könyvek adatait, mint például a szerzőjét, címét, kiadásának
// évét és műfaját.
// - Készíts egy oldalt, ami listázza a könyveket.
// - Készíts egy formot, amivel tetszőlegesen lehet keresni és szűkíteni a könyvek között.

// Megadjuk az adatbázis nevét
$dbname = "info2";

try {
    // Kapcsolódás az adatbázishoz, az adatokat statikusan adjuk meg az egyszerűség kedvéért
    $db = new PDO("mysql:host=localhost;port=3306;dbname=$dbname", "root", "");
    // Beállítjuk, hogy a hibákat kivételként kezelje a PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lekérdezzük az összes egyedi műfajt a műfaj szűrőhöz
    $genres = $db->query("SELECT DISTINCT genre FROM books")->fetchAll(PDO::FETCH_COLUMN);

    // Akkor valósítjuk meg a keresést, ha vannak URL paraméterek
    if (!empty($_GET)) {
        // Keresési feltételek tárolására szolgáló tömb
        $conditions = [];
        // Paraméterek tárolására szolgáló tömb
        $params = [];

        // Szerző szűrés
        if (!empty($_GET['author'])) {
            $conditions[] = "author LIKE :author";
            $params['author'] = '%' . $_GET['author'] . '%';
        }

        // Cím szűrés
        if (!empty($_GET['title'])) {
            $conditions[] = "title LIKE :title";
            $params['title'] = '%' . $_GET['title'] . '%';
        }

        // Év szűrés
        if (!empty($_GET['year']) && !empty($_GET['year_op'])) {
            $opMap = [
                'lt' => '<',
                'eq' => '=',
                'gt' => '>'
            ];
            if (isset($opMap[$_GET['year_op']])) {
                $conditions[] = "year " . $opMap[$_GET['year_op']] . " :year";
                $params['year'] = $_GET['year'];
            }
        }

        // Műfaj szűrés
        if (!empty($_GET['genre'])) {
            $conditions[] = "genre = :genre";
            $params[':genre'] = $_GET['genre'];
        }

        // Összeállítjuk a lekérdezést a feltételekkel
        $select = "SELECT * FROM books";
        if (!empty($conditions)) {
            // Hozzáadjuk a WHERE részt a feltételekkel, a feltételek között AND kapcsolat lesz
            $select .= " WHERE " . implode(" AND ", $conditions);
        }

        // Előkészítjük a lekérdezést
        $stmt = $db->prepare($select);
        // Végrehajtjuk a lekérdezést a paraméterekkel
        $stmt->execute($params);
        // Eredményeket prepared statement esetén a statementből olvasnánk ki, ezt átnevezzük hogy kompatibilis legyen
        // az else ággal is, így nem kell több feltételt kezelni.
        $results = $stmt;
    } else {
        // Alapértelmezett lekérdezés, ha nincsenek keresési feltételek
        $select = "SELECT * FROM books";
        // Mivel nincs paraméter, így sima query-t használunk
        $results = $db->query($select);
    }

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
    <title>Könyvesbolt</title>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
        }

        .book {
            border: 1px solid black;
            padding: 10px;
            margin: 10px;
            width: 200px;
        }

        .author {
            font-style: italic;
        }

        h2 {
            margin: 0;
        }

        fieldset {
            width: 438px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
<fieldset>
    <legend>Keresés</legend>
    <!-- Ez a form most egy GET kérést fog küldeni, mivel a method nincs kitöltve -->
    <!-- Küldés után látjuk is majd, hogy a paraméterek az URL-be kerülnek -->
    <form>
        <label for="author">Szerző:</label>
        <input type="text" id="author" name="author" value="<?= $_GET['author'] ?? '' ?>">
        <br><br>

        <label for="title">Cím:</label>
        <input type="text" id="title" name="title" value="<?= $_GET['title'] ?? '' ?>">
        <br><br>

        <label for="year">Év:</label>
        <!-- Az évre egy <, =, > szűrest alkalmazunk, a megadott dátummal fogjuk összehasonlítani -->
        <select name="year_op" id="year_op">
            <option value="lt" <?= isset($_GET['year_op']) && $_GET['year_op'] == "lt" ? "selected" : ""?>>&lt;</option>
            <option value="eq" <?= isset($_GET['year_op']) && $_GET['year_op'] == "eq" ? "selected" : ""?>>=</option>
            <option value="gt" <?= isset($_GET['year_op']) && $_GET['year_op'] == "gt" ? "selected" : ""?>>&gt;</option>
        </select>
        <input type="number" id="year" name="year" min="1900" value="<?= $_GET['year'] ?? '' ?>">
        <br><br>

        <label for="genre">Műfaj:</label>
        <!-- A műfaj esetében legördülő menüt készítünk -->
        <select name="genre" id="genre">
            <option value="">--válassz--</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= $genre ?>" <?= (isset($_GET['genre']) && $_GET['genre'] == $genre) ? 'selected' : '' ?>>
                    <?= $genre ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="submit">Keresés</button>
        <a href="index.php">Szűrők törlése</a>
    </form>
</fieldset>

<div class="container">
    <!-- A kapott eredményeket listázzuk, jelen példában egy flexbox konténert készítünk -->
    <?php while ($row = $results->fetch(PDO::FETCH_OBJ)): ?>
        <div class="book">
            <h2><?= $row->title ?></h2>
            <p class="author"><?= $row->author ?> (<?= $row->year ?>)</p>
            <p><strong>Műfaj:</strong> <?= $row->genre ?></p>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
