<?php

// --- Feladat: ------------------
// Egy elektronikai webáruház ellenállásokat és kondenzátorokat árul. A termékek ára a típusuktól és értéküktől függ.
// Egy ellenállás egységára 5 Ft, ha az értéke legfeljebb 1000 Ohm, különben 10 Ft. Egy kondenzátor egységára 10 Ft
// plusz az mikrofaradban vett értékének 20%-a (felfele kerekítve). Készíts egy programot, amely egy bevásárlókosár
// tartalmát jeleníti meg táblázatos formában, és kiszámítja a végösszeget is! Használj osztályokat és öröklődést a
// feladat megoldására!
// -------------------------------

// Absztrakt osztály a közös tulajdonságokkal és metódusokkal, mivel abstract, ezért önmagában nem lehet példányosítani
abstract class Part
{
    // A változók nyilvánosak, hogy a leszármazott osztályok is elérhessék őket, itt tároljuk az alaktrész darabszámát és értékét
    public int $value;
    public int $quantity;

    // A konstruktor, ami inicializálja a változókat példányosításkor
    public function __construct($value, $quantity)
    {
        // A változókra -> operátorral hivatkozunk az objektumon belül, a this kulcsszó az aktuális objektumot jelöli
        $this->value = $value;
        $this->quantity = $quantity;
    }

    // Absztrakt metódusok, amiket a leszármazott osztályoknak kötelező megvalósítaniuk
    // Ez fogja az egységárat meghatározni
    public abstract function unit(): int;

    // Ez fogja a termék nevét meghatározni
    public abstract function name(): string;

    // Ez fogja kiszámítani a teljes árat (egységár * mennyiség)
    public function price(): int
    {
        return $this->unit() * $this->quantity;
    }
}

// Leszármazott osztály az ellenállásokhoz
class Resistor extends Part
{
    // Az ellenállás értéke 5 Ft, ha < 1000 Ohm, különben 10 Ft
    public function unit(): int
    {
        // Használjuk a (feltétel ? igaz : hamis) szerkezetet
        return $this->value > 1000 ? 10 : 5;
    }

    // A nevet string interpoláció segítségével állítjuk elő
    public function name(): string
    {
        return "Ellenállás ($this->value R)";
    }
}

// Leszármazott osztály a kondenzátorokhoz
class Capacitor extends Part
{
    // A kondenzátor egységára 10 Ft + 20% az értékből, felfelé kerekítve, a felfele kerekítéshez a ceil függvényt használjuk
    public function unit(): int
    {
        return 10 + ceil(0.2 * $this->value);
    }

    // A nevet string interpoláció segítségével állítjuk elő itt is
    public function name(): string
    {
        return "Kondenzátor ($this->value uF)";
    }
}

// Az osztályok teszteléséhez létrehozunk egy tömböt osztályokkal. Példányosításkor az egyes példányok konstruktorai
// hívódnak meg. Ezt mi nem írtuk most felül, ezért az ősosztály konstruktora hívódik meg automatikusan.
$cart = [
    new Resistor(220, 100),
    new Resistor(4700, 30),
    new Capacitor(10, 50)
];

// Létrehozunk egy változót a végösszeg tárolására
$total = 0;
// Kiírjuk a táblázat fejlécét, a \t tabulátor karaktereket használunk az oszlopok elválasztására, sor végén pedig \n a sortörés.
echo "Név\tEgységár\tMennyiség\tÁr\n";
// Végigiterálunk a kosár elemein
foreach ($cart as $item) {
    // Meghívjuk a price() metódust, ami kiszámolja az adott elem árát
    $price = $item->price();
    // Hozzáadjuk az árát a végösszeghez
    $total += $price;
    // Kiírjuk az adott elem adatait, pl: nevét, egységárát, mennyiségét és árát, hasonlóan a fejléchez
    echo "{$item->name()}\t{$item->unit()} Ft\t$item->quantity db\t$price Ft\n";
}
// Végül kiírjuk a végösszeget
echo "Összesen: $total Ft\n";


