<?php
require 'lib/helpers.php';

$db = connectDatabase();

$query = "SELECT id, nev, IFNULL(SUM(mt.db),0) db, raktarkeszlet, (raktarkeszlet - IFNULL(SUM(mt.db),0)) keszlet FROM termekek t 
              LEFT JOIN megrendeles_tetelek mt on t.id=mt.termek_id
              GROUP BY t.id, t.nev";

$result = $db->query($query);
?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Webshop</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
              crossorigin="anonymous">
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Webshop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Főoldal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="termekek.php">Termékek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vevok.php">Vevők</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="megrendelesek.php">Megrendelések</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1>Termékek</h1>
        <hr class="col-1 my-4">
        <table class="table">
            <tr>
                <th>#</th>
                <th>Név</th>
                <th>Rendelések száma</th>
                <th>Részletek</th>
            </tr>

            <tbody class="table-group-divider">
            <?php while ($obj = $result->fetchObject()): ?>
                <tr>
                    <td><?= $obj->id ?></td>
                    <td><?= $obj->nev ?></td>
                    <td
                        <?= $obj->keszlet < 0 ? 'class="text-danger fw-bold"' : '' ?>
                    ><?= $obj->db ?> / <?= $obj->raktarkeszlet ?></td>
                    <td><a href="megrendelesek.php?termek_id=<?= $obj->id ?>">Megrendelés</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    </body>
    </html>
<?php
$result->closeCursor();
unset($db);
?>