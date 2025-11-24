<?php

// Egy stopperóra megvalósításához weboldalt készítünk.
// - Készíts egy oldalt rajta egy gombbal, amivel el lehet indítani egy stopperórát.
// - Indításkor session-be tároljuk el az indítás idejét.
// - Ha a stopperóra fut, akkor egy stop gombot jelenítünk meg.
// - A stop gomb megnyomása esetén kiírjuk az eltelt időt.

// Indítjuk a session-t
session_start();

// Változó a futás állapotának jelzésére (alapértelmezett érték)
$run = false;

// Ellenőrizzük, hogy a stopperóra fut-e
if (isset($_SESSION['start'])) {
    $run = true;
}

// Feldolgozzuk a form beküldését
if (isset($_POST['action'])) {
    // Kiolvassuk a konkrét műveletet
    $action = $_POST['action'];

    // Indítás esetén elmentjük az aktuális időt a session-be
    if ($action == "start" && !$run) {
        $_SESSION['start'] = time();
        $run = true;
    } else {
        // Leállítás esetén kiszámoljuk és kiírjuk az eltelt időt
        $start = $_SESSION['start'];
        $end = time();
        $diff = $end - $start;
        echo "Eltelt idő: $diff másodperc";

        // Töröljük a session adatot
        unset($_SESSION['start']);
        $run = false;
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stopperóra</title>
</head>
<body>
<!--A stopperóra állapota alapján vagy start vagy stop gombot jelenítünk meg-->
<form method="post">
    <?php if ($run): ?>
        <input type="submit" name="action" value="stop">
    <?php else: ?>
        <input type="submit" name="action" value="start">
    <?php endif; ?>
</form>

</body>
</html>
