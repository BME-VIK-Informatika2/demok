<?php

// Egy weboldal stílusát a felhasználó egy kapcsoló segítségével változtathatja.
// - Készíts egy oldalt, amin található egy kapcsoló (checkbox).
// - A kapcsoló egyik állapotában a weboldal háttere legyen kék színű, másik esetben pedig zöld.
// - A kapcsoló aktuális értékét mentsd el egy sütibe és az oldal betöltésekor onnan olvasd be.


// Alapértelmezett háttérszín
$color="green";

// Ha létezik a cookie, akkor állítsuk be a háttérszínt a cookie értékére
if(isset($_COOKIE["color"])){
    $color=$_COOKIE["color"];
}

// Ha a form be lett küldve, állítsuk be a cookie-t az új színnel és frissítsük az oldalt
if(!empty($_POST)){
    if(isset($_POST['color']) && $_POST['color'] === 'blue'){
        // Beállítjuk a cookie-t egy órára (1 óra = 3600 másodperc)
        setcookie("color", 'blue', time() + 3600);
    } else {
        // Töröljük a cookie-t, az érték nem releváns, a lejárati idő múltban van, ez számít
        setcookie("color", '', time() - 3600);
    }

    // Oldal frissítése (átirányítás önmagára)
    header("Location: {$_SERVER['PHP_SELF']}");
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Változtatható stílus</title>
</head>
<!-- Beállítjuk a háttérszínt a változó értékének megfelelően -->
<body style="background-color:<?=$color?>">
    <h1>Változtatható háttérszínű oldal</h1>
    <form method="POST">
        <label for="color">A háttérszín legyen kék:</label>
        <input type="checkbox" name="color" id="color" value="blue" <?= $color === "blue" ? "checked" : "" ?>>
        <br><br>
        <input type="submit" name="submit">
    </form>
</body>
</html>

