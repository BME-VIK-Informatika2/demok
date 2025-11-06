<?php

// --- Feladat: ------------------
// Készíts egy regisztrációs űrlapot, ami képes feldolgozni a felhasználó által megadott adatokat!
// Az űrlap tartalmazza a név, email, születési év, születési hely mezőket. Ellenőrizd az adatok helyességét az alábbi
// szabályok szerint:
// - Név: Két részből áll, mindkét rész nagybetűvel kezdődik, majd kisbetűk követik, és egy szóköz választja el őket.
// - Email: érvényes email cím formátum
// - Születési év: szám, az illető min 18, max 100 éves lehet
// - Születési hely: nem kötelező megadni, de ha meg van adva, akkor csak az alábbi települések egyikét tartalmazhatja:
//      Budapest, Debrecen, Szeged, Miskolc, Pécs
// Ha minden adat helyes, jelenítsd meg a felhasználó által megadott adatokat, különben jelezd a hibákat!
// -------------------------------

// Definiáljuk a megengedett városokat
$cities = ["Budapest", "Debrecen", "Szeged", "Miskolc", "Pécs"];
// Létrehozunk egy üres tömböt a hibák tárolására
$errors = [];

// Ellenőrizzük, hogy az űrlapot elküldték-e, csak akkor futtatjuk ha igen, egyéb esetben üres lesz a $_POST tömb
if (!empty($_POST)) {
    // Inicializáljuk a változókat
    $name = null;
    $email = null;
    $date = null;
    $city = null;

    // Validáció #1 Név
    // Ha nincs kitöltve a név mező, hozzáadunk egy hibát a tömbhöz
    if (empty($_POST['name'])) {
        $errors['name'] = "Név megadása kötelező!";
    } else {
        // Ha ki van töltve, akkor először átalakítunk minden speciális karaktert, így nem lehet bevinni nem kívánatos tartalmat
        $name = htmlspecialchars($_POST['name']);
        // Ellenőrizzük a név formátumát reguláris kifejezéssel, ennek részei:
        // ^ - a string eleje
        // [A-Z] - egy nagybetű az elején
        // [a-z]+ - egy vagy több kisbetű
        //  - egy szóköz
        // [A-Z] - egy nagybetű a második név elején
        // [a-z]+ - egy vagy több kisbetű
        // $ - a string vége
        if (!preg_match('/^[A-Z][a-z]+ [A-Z][a-z]+$/', $name)) {
            // Hibás formátum esetén beírjuk a hibát
            $errors['name'] = "Hibás név formátum!";
        }
    }

    // Validáció #2 E-mail
    // Ha nincs kitöltve az e-mail mező, hozzáadunk egy hibát a tömbhöz
    if (empty($_POST['email'])) {
        $errors['email'] = "E-mail megadása kötelező!";
    } else {
        // Ellenőrizzük az e-mail formátumát a beépített filterrel, ha sikeres, akkor beleírja az értéket a változóba
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        // Sikertelen validáció esetén, ilyenkor a false érték kerül be a változóba, beírjuk a hibát
        if ($email === false) {
            $errors['email'] = "Hibás e-mail formátum!";
        }
    }

    // Validáció #3 Születési idő
    // Ha nincs kitöltve a születési idő mező, hozzáadunk egy hibát a tömbhöz
    if (empty($_POST['date'])) {
        $errors['date'] = "Születési idő megadása kötelező!";
    } else {
        // Átalakítjuk a dátumot timestamp formátumba (ez php-ban egy objektum lesz)
        $date = strtotime($_POST['date']);
        // Meghatározzuk ebből az évet, úgy hogy formázzuk a dátumot csak évre, majd egész számmá alakítjuk
        $birthYear = (int)date('Y', $date);
        // Megismételjük ugyanezt az aktuális dátumra is, ha nincs megadva dátum az mindig a mai nap lesz
        $currentYear = (int)date('Y');
        // Kiszámoljuk az életkort a kettő különbségével
        $age = $currentYear - $birthYear;
        // Ellenőrizzük az életkort a megadott feltételek szerint, ha nem teljesül, ismét hibát veszünk fel
        if ($age < 18 || $age > 100) {
            $errors['date'] = "Csak 18 és 100 év közötti személyek regisztrálhatnak!";
        }
    }

    // Validáció #4 Születési hely
    // Ez egy opcionális szabály nincs hiba, ha nincs kitöltve, viszont csak akkor validálunk, ha ki van töltve
    // A validáció magába foglalja azt, hogy megnézzük, hogy a megadott város benne van-e a megengedett városok tömbjében
    if (!empty($_POST['city']) && !in_array($_POST['city'], $cities)) {
        // Ha nincs benne, akkor hibát veszünk fel
        $errors['city'] = "Hibás születési hely!";
    } else {
        // Ha benne van, elmentjük
        // Mivel itt viszonylag kötött mi megy át, nem lenne feltétlenül szükséges a karakterek szűrése, de hibát nem követünk el vele
        $city = htmlspecialchars($_POST['city']);
    }

    // Ha eljutottunk ide úgy, hogy nincs hiba, tehát a tömb üres, akkor minden adat helyes és kiírhatjuk az eredményt
    if (empty($errors)) {
        // String interpolációt alkalmazunk
        echo "Név: $name<br>";
        echo "E-mail: $email<br>";
        // String összefűzést alkalmazunk
        // Dátum formázásnál az Y-m-d formátumot használjuk, ami év-hónap-nap sorrendet jelent
        echo "Születési idő: " . date('Y-m-d', $date) . "<br>";
        // A hely esetén használunk egy rövid feltételes operátort is, ha nincs megadva, akkor kiírjuk, hogy nincs megadva
        // A forma itt ( kifejezés ?? alapértelmezett érték ) lesz. A kifejezés kiértékelődik, és ha az értéke null, akkor fogja az alapértelmezettet kiírni
        echo "Születési hely: " . ($city ?? 'Nincs megadva') . "<br>";
        // Kilépünk a scriptből, hogy ne jelenjen meg újra az űrlap, 0-ás kóddal jelezzük a sikeres befejezést
        exit(0);
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regisztráció</title>
</head>
<body>
<!-- A form metódusa POST, vagyis a mezők értékei a kérés törzsébe kerülnek -->
<form method="post">
    <label for="name">Név:</label>
    <!-- Kliens oldali validációt valósítunk meg, a required attribútum szavatolja, hogy ne is lehessen üresen elküldeni a formot -->
    <!-- Hiba esetén visszaírjuk az eredeti értéket a value attribútum segítségével, a ?? operátor szavaotolja, hogy ne legyen hiba akkor se ha nincs kitöltve -->
    <input type="text" id="name" name="name" value="<?= $_POST['name'] ?? '' ?>" required>
    <!-- Ha hiba van, vagyis a kulcs megjelenik az $errors tömbben, megjelenítünk egy hibaüzenetet -->
    <?php if (isset($errors['name'])): ?>
        <span style="color: red;"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>" required>
    <?php if (isset($errors['email'])): ?>
        <span style="color: red;"><?= $errors['email'] ?></span>
    <?php endif; ?>
    <br><br>

    <label for="date">Születési idő:</label>
    <input type="date" id="date" name="date" value="<?= $_POST['date'] ?? '' ?>" required>
    <?php if (isset($errors['date'])): ?>
        <span style="color: red;"><?= $errors['date'] ?></span>
    <?php endif; ?>
    <br><br>

    <label for="city">Születési hely:</label>
    <!-- A select egy olyan mező, ami legördülő listát valósít meg, innen lehet választani csak értéket -->
    <select name="city" id="city">
        <!-- Az első opció a default, a value üres, tehát nincs értéke -->
        <option value="">-- Válassz --</option>
        <!-- Listázzuk a megengedett városokat egy ciklussal -->
        <?php foreach ($cities as $city): ?>
            <!-- A value attribútum lesz az ami elküldésre kerül, a megjelenő szöveg pedig a város neve -->
            <!-- Hiba esetén ha vissza akarjuk állítani az értéket, akkor ellenőrizzük, hogy a POST-ban lévő érték megegyezik-e az aktuális várossal -->
            <!-- Ha igen, akkor hozzáadjuk a selected attribútumot, ami kijelöli az adott opciót -->
            <option value="<?= $city ?>" <?= (isset($_POST['city']) && $_POST['city'] === $city) ? 'selected' : '' ?>><?= $city ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($errors['city'])): ?>
        <span style="color: red;"><?= $errors['city'] ?></span>
    <?php endif; ?>
    <br><br>

    <input type="submit" value="Regisztráció">
</form>
</body>
</html>
