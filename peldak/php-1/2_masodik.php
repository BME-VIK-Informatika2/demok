<?php

// --- Feladat: ------------------
// Egy webalkalmazásban a felhasználói szerepkörök (admin, szerkesztő, vendég) különböző jogosultságokkal rendelkeznek
// (létrehoz, olvas, módosít, töröl). Az admin minden jogosultsággal rendelkezik, az editor csak olvasási és módosítási
// jogokkal, míg a vendég csak olvasási jogokkal bír. Írj egy PHP függvényt, aminek segítségével ellenőrzni lehet, hogy
// az alábbi állítások közül melyek helyesek:
// - Az admin jogosult létrehozni új tartalmat.
// - A szerkesztő jogosult módosítani meglévő tartalmat.
// - A vendég tud tartalmat eltávolítani.
// - Az editor tud létrehozni újat.
// - A vendég csak olvasni tud.
// -------------------------------

function checkPermission($roles, $statment): bool
{
    // Végigiterálunk a szerepkörökön és jogosultságokon
    foreach ($roles as $role => $perms) {
        // Ha a mondat tartalmazza a szerepkör nevét ...
        if (str_contains($statment, $role)) {
            // Végigiterálunk a jogosultságokon (mivel ez egy tömb)
            foreach ($perms as $perm) {
                // Ha a mondat tartalmazza a jogosultság nevét ...
                if (str_contains($statment, $perm)) {
                    // Visszatérünk egy igaz értékkel, azonnal kilépünk a ciklusokból
                    return true;
                }
            }
        }
    }
    // Ha végére értünk a ciklusoknak, és nem léptünk eddig ki, akkor hamis értékkel térünk vissza
    return false;
}

// Definiáljuk a szerepköröket és jogosultságaikat egy többdimenziós tömbben
$roles = [
    "admin" => ["létrehoz", "olvas", "módosít", "töröl"],
    "szerkesztő" => ["olvas", "módosít"],
    "vendég" => ["olvas"],
];

// Felsoroljuk az állításokat
$messages = [
    "Az admin jogosult létrehozni új tartalmat.",
    "A szerkesztő jogosult módosítani meglévő tartalmat.",
    "A vendég tud tartalmat eltávolítani.",
    "A szerkesztő tud létrehozni újat.",
    "A vendég csak olvasni tud.",
];

// A ciklussal végigmegyünk az állításokon és minden sort a $msg változóba helyezünk
foreach ($messages as $msg) {
    // Ellenőrizzük az állítást a checkPermission függvénnyel, ennek átadjuk a szerepköröket és az aktuális állítást és
    // ez egy logikai értéket ad vissza
    if (checkPermission($roles, $msg)) {
        // Ha igaz, akkor kiírjuk az állítást, egyéb esetben nem fog történni semmi.
        echo "Az állítás helyes: $msg\n";
    }
}