<?php

// Egy konferencián adatbázisban tároljuk a szekciókat és az azokon résztvevőket.
// - Készíts egy oldalt, ahol az adott szekció jelenléti ívét lehet kitölteni.

// Ellenőrizzük, hogy meg van-e adva az id paraméter az URL-ben
if (!isset($_GET["id"])) {
    // Ha nincs, akkor visszairányítjuk a főoldalra
    header("Location: index.php");
}
$session_id = intval($_GET["id"]);

// Megadjuk az adatbázis nevét
$dbname = "info2";

try {
    // Kapcsolódás az adatbázishoz, az adatokat statikusan adjuk meg az egyszerűség kedvéért
    $db = new PDO("mysql:host=localhost;port=3306;dbname=$dbname", "root", "");
    // Beállítjuk, hogy a hibákat kivételként kezelje a PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ellenőrizzük, hogy létezik-e ilyen szekció
    $stmt = $db->prepare("SELECT * FROM sessions WHERE id = :id");
    $stmt->execute(['id' => $session_id]);
    if ($stmt->rowCount() == 0) {
        $stmt->closeCursor();
        // Ha nincs ilyen szekció, akkor visszairányítjuk a főoldalra
        header("Location: index.php");
    }
    // Lekérjük a szekció adatait
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Lekérdezzük a résztvevőket, és a státuszukat az adott szekcióra vonatkozóan
    $att_stmt = $db->prepare("
        SELECT a.*, COUNT(att.session_id) AS attendance 
        FROM attendees a
        LEFT JOIN attendance att ON a.id = att.attendee_id AND att.session_id = :session_id
        GROUP BY a.id
        ORDER BY a.name ASC
    ");
    $att_stmt->execute(['session_id' => $session_id]);
    $attendees = $att_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_POST)) {
        // Kiürítjük a jelenlét táblát az adott szekcióra vonatkozóan, hogy újra lehessen rögzíteni a jelenlétet
        // Alternatívaként lehetne egyesével ellenőrizni és beszúrni/törölni is a bejegyzéseket
        $delete_stmt = $db->prepare("DELETE FROM attendance WHERE session_id = :session_id");
        $delete_stmt->execute(['session_id' => $session_id]);
        $delete_stmt->closeCursor();

        // Előkészítünk egy utasítást a résztvevő ellenőrzésére
        $select_stmt = $db->prepare("SELECT * FROM attendees WHERE id = :id");
        // Előkészítünk egy utasítást a jelenlét beillesztésére
        $insert_stmt = $db->prepare("INSERT INTO attendance (session_id, attendee_id) VALUES (:session_id, :attendee_id)");

        // Végigmegyünk a POST adatokon, a kulcs lesz a résztvevő azonosítója, az érték pedig a jelenlét státusza
        foreach ($_POST as $attendee_id => $attendance) {
            // Csak akkor dolgozzuk fel, ha jelen volt
            if ($attendance == 'present') {
                // Ellenőrizzük, hogy létezik-e ilyen résztvevő
                $select_stmt->execute(['id' => $attendee_id]);
                if ($select_stmt->rowCount() > 0) {
                    // Ha létezik, akkor beillesztjük a jelenlétet
                    $insert_stmt->execute([
                        'session_id' => $session_id,
                        'attendee_id' => $attendee_id
                    ]);
                }
            }
        }

        // Lezárjuk az utasításokat
        $select_stmt->closeCursor();
        $insert_stmt->closeCursor();
        // Visszairányítjuk a főoldalra
        header("Location: index.php");
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
    <title>Jelenlét</title>
    <style>
        table {
            margin-bottom: 10px;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        .cell-center {
            text-align: center;
        }
    </style>
</head>
<body>
<h1>Szekció jelenlét: <?= $session['name'] ?></h1>
<!-- Létrehozunk egy űrlapot, amibe egy egész táblázatot rakunk bele -->
<form method="post">
    <table>
        <tr>
            <th>Név</th>
            <th class="cell-center">Megjelent</th>
            <th class="cell-center">Nem jelent meg</th>
        </tr>
        <!-- Listázzuk a résztvevőket -->
        <?php foreach ($attendees as $attendee): ?>
            <tr>
                <td><?= $attendee['name'] ?></td>
                <!--
                    Minden résztvevő neve mellett meg fog jelenni 2 rádió gomb. Ezek közül csak az egyiket lehet
                    kiválasztani. Ezt onnan fogja tudni, hogy mindkét gombnak azonos lesz a name attribútuma, jelen
                    esetben a résztvevő azonosítója. A value attribútum határozza meg, hogy melyik gomb mit jelent, ez
                    fog megjelenni a $_POST tömbben. A checked attribútumot dinamikusan állítjuk be annak megfelelően,
                    hogy a résztvevő jelen volt-e vagy sem.
                -->
                <td class="cell-center">
                    <input type="radio" name="<?= $attendee['id'] ?>"
                           value="present" <?= $attendee['attendance'] == 1 ? 'checked' : '' ?>>
                </td>
                <td class="cell-center">
                    <input type="radio" name="<?= $attendee['id'] ?>"
                           value="absent" <?= $attendee['attendance'] == 0 ? 'checked' : '' ?>>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <input type="submit" value="Mentés">
</form>
</body>
</html>
