<?php

// Egy versenyen a feladott rejtvény helyes megoldását egy jelszóval védett oldalon teszik közzé a verseny zsűrije számára.
// - Készíts egy oldalt ami egy jelszót vár ahhoz, hogy felfedje a tartalmát.
// - A jelszót hashelve tároljuk, hogy a forráskódból se lehessen megtudni.

// A password_hash('jelszo', PASSWORD_DEFAULT) segítségével létrehozott jelszó hash, ami biztonságosan tárolható adatbázisban
$hash = '$2y$10$bUUhk.X9AHumfYbCTmkFSOk/2duXW3EiryrvtSabvq/zNNnr4uA4.'; //jelszo
$correct = false;
$errors = [];

// Ellenőrizzük, hogy a jelszó be lett-e küldve
if(isset($_POST["pwd"])){
    $pwd = $_POST["pwd"];

    // Ellenőrizzük a jelszót a hash segítségével
    if(password_verify($pwd, $hash)){
        $correct = true;
    } else {
        $errors["pwd"] = "Hibás jelszó!";
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verseny megoldás</title>
</head>
<body>
<?php if ($correct): ?>
    <h1>A megoldás</h1>
    <p>...</p>
<?php else: ?>
    <form method="post">
        <label for="pwd">Jelszó:</label>
        <input type="password" name="pwd" id="pwd">
        <?php if (isset($errors["pwd"])): ?>
            <span style="color: red;"><?php echo $errors["pwd"]; ?></span>
        <?php endif; ?>
        <br><br>
        <input type="submit" value="Belépés">
    </form>
<?php endif; ?>
</body>
</html>

