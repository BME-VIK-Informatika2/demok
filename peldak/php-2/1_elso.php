<?php

// --- Feladat: ------------------
// Készíts egy PHP programot, ami kiírja táblázatos formában az URL-ben megadott szám első 5 többszörösét! Emeljük ki a
// táblázatban az összes páros eredményű sort világoskék háttérrel!
// -------------------------------

// Létrehozunk egy változót a szám tárolására, alapértelmezetten null értékkel
$number = null;
// Ellenőrizzük, hogy az URL-ben meg van-e adva a "number" paraméter, és hogy az numerikus-e
if (isset($_GET["number"]) && is_numeric($_GET["number"])) {
    // Ha igen, akkor átalakítjuk egész számmá és eltároljuk a változóban
    $number = intval($_GET["number"]);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Szorzótábla</title>
</head>
<body>
<!-- Ha a number változó értéke nics megadva, akkor jelenítsünk meg egy formot amivel könnyen be lehet állítani -->
<!-- Használjuk az IF alternatív formulát, tehát nem kapcsos zárójel van, hanem if(...): ... else:  ... endif; -->
<?php if ($number === null): ?>
    <!-- A formon nincs beállítva se az action, se a method, vagyis saját magának küldi a kérést és GET lesz-->
    <form>
        <label for="number">Adj meg egy számot:</label>
        <!-- A name attribútum lesz az ami majd megjelenik az URL-ben, pl: 1_elso.php?number=5-->
        <input type="number" id="number" name="number" required>
        <button type="submit">Küldés</button>
    </form>
<?php else: ?>
    <table border="1">
        <tr>
            <th>Szorzó</th>
            <th>Szám</th>
            <th>Eredmény</th>
        </tr>
        <!-- For ciklussal iterálunk 5-ig, minden egyes iterációban kiírunk egy új sort -->
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <!-- Ha az eredmény páros, akkor állítsuk be a háttérszínt világoskékre, itt is a (feltétel ? igaz : hamis) formát használjuk-->
            <!-- A párosságot úgy ellenőrizzük, hogy az eredményt elosztjuk 2-vel és megnézzük, hogy van-e maradék -->
            <!-- Ha igaz lesz a feltétel, akkor hozzáadjuk a style attribútumot a tr elemhez, ha nem akkor nem írunk ki semmit -->
            <tr <?= ($i * $number) % 2 == 0 ? 'style="background-color: lightblue;"' : '' ?>>
                <!-- Kiírjuk az aktuális szorzót, a megadott számot és az eredményt a rövid szintaktikával, nem használunk echo-t -->
                <td><?= $i ?></td>
                <td><?= $number ?></td>
                <td><?= $i * $number ?></td>
            </tr>
        <?php endfor; ?>
    </table>
<?php endif; ?>
</body>
</html>
