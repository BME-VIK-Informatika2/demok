<?php

// --- Feladat: ------------------
// Készíts egy PHP programot, aminek fel lehet tölteni egy CSV fájlt, majd a program átalakítja JSON formátumba és
// megjeleníti a felhasználónak!
// -------------------------------

// Ellenőrizzük, hogy a felhasználó elküldte-e a formot és van benne egy delimiter mező és egy csv fájl
if(isset($_POST['delimiter']) && isset($_FILES['csv'])){
    // Kiolvassuk az elválasztó karaktert
    $delimiter = $_POST['delimiter'];
    // Kiolvassuk a feltöltött fájl adatait
    $file = $_FILES['csv'];

    // Ellenőrizzük, hogy sikeres volt-e a feltöltés (ilyenkor az errorban a kód 0) és hogy valóban CSV fájl-e
    if($file['error'] === 0 && $file['type'] === 'text/csv'){
        // Létrehozunk egy üres tömböt a tartalom tárolására
        $content = [];
        // Megnyitjuk a fájlt olvasásra, a fájl tényleges elérési útja a tmp_name mezőben van (valami tmp mappa a szerveren)
        $handler = fopen($file['tmp_name'], "r");
        // Beolvassuk az első sort, ami a fejléc sor lesz
        $header = fgetcsv($handler, null, $delimiter);
        // Amíg nem érünk a fájl végére, beolvassuk a sorokat, a feof az a "file end of file" ellenőrzés
        while(!feof($handler)){
            // Beolvassuk a következő sort a megadott elválasztó karakterrel, az eredményt a $line változóba rakjuk, mint indexelt tömb
            $line = fgetcsv($handler, null, $delimiter);
            // Az array_combine segítségével tudunk egy asszociatív tömböt létrehozni két indexelt tömb segítségével
            // A kulcsok lesznek az első tömbben, az értékek a második tömbben:
            // array_combine(['name', 'age'], ['John', '30']) => ['name' => 'John', 'age' => '30']
            // Az így kapott asszociatív tömböt hozzáadjuk a tartalom tömbhöz, a [] automatikusan hozzáfűzi az új elemet a tömb végéhez
            $content[] = array_combine($header, $line);
        }
        // Bezárjuk a fájlt
        fclose($handler);

        // Beállítjuk a válasz típusát JSON-ra
        header('Content-type: application/json');
        // Az asszociatív tömböt JSON formátumba alakítjuk a json_encode függvénnyel és kiírjuk az echo-val.
        echo json_encode($content);
        // Ha sikeres volt a feldolgozás, akkor kilépünk a programból 0-as státusszal (sikerrel)
        exit(0);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSV konverter</title>
</head>
<body>
<!-- Ha fájlt akarunk feltölteni, akkor a metódus kötelezően POST és be kell állítani az enctype attribútumot -->
<form method="POST" enctype="multipart/form-data">
    <label for="csv">CSV fájl feltöltése:</label>
    <!-- Ha szeretnénk csak kifejezetten adott formátumú fájl feltöltését megengedni, akkor az accept attribútumot használjuk -->
    <input type="file" id="csv" name="csv" accept=".csv" required>
    <br><br>

    <label for="delimiter">Elválasztó karakter</label>
    <input type="text" id="delimiter" name="delimiter" value="," maxlength="1" required>
    <br><br>

    <button type="submit">Feltöltés</button>
</form>
</body>
</html>
