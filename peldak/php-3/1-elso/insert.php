<?php

// Egy adatgyűjtő rendszerben adatbázisban tároljuk szenzorok megfigyeléseit.
// - Készíts egy oldalt, ahol új megfigyelést lehet felvinni egy adott szenzorhoz

// Megadjuk az adatbázis nevét
$dbname = "info2";

// A hibák tárolására létrehozunk egy tömböt
$errors = [];

try {

    // Kapcsolódás az adatbázishoz, az adatokat statikusan adjuk meg az egyszerűség kedvéért
    $db = new PDO("mysql:host=localhost;port=3306;dbname=$dbname", "root", "");
    // Beállítjuk, hogy a hibákat kivételként kezelje a PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Az összes szenzort lekérdezzük az adatbázisból, hogy meg tudjuk jeleníteni a legördülő mezőben, ezt egy sorban
    $sensors = $db->query("SELECT * FROM sensors")->fetchAll(PDO::FETCH_ASSOC);

    // Ha az URL-ben van megadva egy szenzor azonosító, akkor eltároljuk egy változóban, egyébként null lesz az értéke
    $sensor_id = null;
    if (isset($_GET['id'])) {
        // Az URL-ben megadott értéket a $_GET szuperglobális változóból olvassuk ki
        $sensor_id = $_GET['id'];
    }

    // Ha az űrlap elküldésre került, akkor a $_POST tömb nem lesz üres
    if (!empty($_POST)) {
        // Ellenőrizzük a szenzor mező meg van-e adva
        $sensor_id = $_POST['sensor'];
        if (!empty($sensor_id)) {
            // Ellenőrizzük, hogy létezik-e ilyen szenzor az adatbázisban
            $statement = $db->prepare("SELECT COUNT(id) FROM sensors WHERE id = :id");
            $statement->bindParam(":id", $sensor_id);
            $statement->execute();
            // Az SQL lekérdezés csak egy számot ad vissza, így nem szükséges feldolgozni az összeset,
            // elég az első oszlopot lekérdezni
            if ($statement->fetchColumn() == 0) {
                $errors['sensor'] = 'Nincs ilyen szenzor!';
            }
            $statement->closeCursor();
        } else {
            $errors['sensor'] = 'Nincs megadva szenzor!';
        }

        // Ellenőrizzük az érték mezőt, hogy meg van-e adva
        $value = $_POST['value'];
        if (!empty($value)) {
            // Ellenőrizzük, hogy szám-e az érték
            $value = filter_var($value, FILTER_VALIDATE_FLOAT);
            if ($value === false) {
                $errors['value'] = 'Hibás érték formátum!';
            }
        } else {
            $errors['value'] = 'Nincs megadva érték!';
        }

        // Ha nincsenek hibák, akkor beszúrjuk az új megfigyelést az adatbázisba
        if (empty($errors)) {
            // Beszúrásra prepared statementet használunk
            $insert = "INSERT INTO observations (sensor_id, value) VALUES (:sensor_id, :value)";
            $statement = $db->prepare($insert);
            // Az értékek egy változóban vannak, ezért a bindParam-al kötjük a paramétereket a helyörzőkhöz
            $statement->bindParam(":sensor_id", $sensor_id);
            $statement->bindParam(":value", $value);
            // Lefuttatjuk a kérést, ekkor kerül be az adatbázisba az új sor
            $statement->execute();
            $statement->closeCursor();
            // Ha minden sikeres volt, akkor átirányítjuk a főoldalra
            header("Location: index.php");
            exit(0);
        }
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
    <title>Szenzorok listázása</title>
</head>
<body>
<!-- Létrehozunk egy űrlapot új érték beszúrására, az űrlep POST kérést fog küldeni -->
<form method="post">
    <label for="sensor">Szenzor</label>
    <!-- A szenzor kiválasztható egy lenyíló mezővel -->
    <select name="sensor" id="sensor">
        <!-- Az opciókat az adatbázisban találhatóak alapján fogjuk listázni -->
        <?php foreach ($sensors as $sensor): ?>
            <!-- Az űrlap a szenzor azonosítóját fogja elküldeni, mivel ez van a value attribútumban -->
            <!-- Ha a linkben meg van adva az azonosító, akkor kiválasztjuk azt az opciót a selected attribútummal -->
            <option value="<?= $sensor['id'] ?>" <?= ($sensor_id == $sensor['id']) ? 'selected' : '' ?>>
                <!-- A legördülő mezőben a név fog megjelenni -->
                <?= $sensor['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <!-- Hiba esetén kiírjuk a hibát -->
    <?php if (isset($errors['sensor_id'])): ?>
        <span style="color: red;"><?= $errors['sensor_id'] ?></span>
    <?php endif; ?>
    <br><br>
    <label for="value">Érték</label>
    <!-- Az értéknek egy szám típusú mezőt veszünk fel, hiba esetén vissza fogjuk írni -->
    <input type="number" name="value" id="value" value="<?= $value ?? '' ?>" step="0.01">
    <!-- Hiba esetén kiírjuk a hibát -->
    <?php if (isset($errors['value'])): ?>
        <span style="color: red;"><?= $errors['value'] ?></span>
    <?php endif; ?>
    <br><br>
    <button type="submit">Mentés</button>
</form>
</body>
</html>
