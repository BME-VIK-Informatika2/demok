<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/db/models.php';

use Illuminate\Database\Capsule\Manager as Capsule;

connectDatabase();

$errors = [];
// Megrendelés mentése
if (!empty($_POST)) {
    $megrendeles_termek = $_POST['termek'];
    $megrendeles_vevo = $_POST['vevo'];
    $megrendeles_db = $_POST['db'];

    $termek = Termek::find($megrendeles_termek);

    if ($termek == null) {
        $errors['termek'] = 'Nincs ilyen termék!';
    }

    $vevo = Vevo::find($megrendeles_vevo);
    if ($vevo == null) {
        $errors['vevo'] = 'Nincs ilyen vevő!';
    }

    $megrendeles_db = filter_var($megrendeles_db, FILTER_VALIDATE_INT);
    if ($megrendeles_db === false) {
        $errors['db'] = 'A darabszámnak számnak kell lennie!';
    } else if ($megrendeles_db < 1) {
        $errors['db'] = 'A darabszámnak legalább 1-nek kell lennie!';
    }

    if (empty($errors)) {

        try {
            Capsule::connection()->transaction(function () use ($vevo, $termek, $megrendeles_db) {
                $megrendeles = new Megrendeles();
                $megrendeles->datum = date('Y-m-d');
                $vevo->megrendelesek()->save($megrendeles);
                $megrendeles->termekek()->attach($termek, ['db' => $megrendeles_db]);
            });

            $status = 'Sikeres mentés!';
            unset($megrendeles_termek, $megrendeles_vevo, $megrendeles_db);
        } catch (Exception $e) {
            $status = $e->getMessage();
            $errors[] = false;
        }
    } else {
        $status = 'Sikertelen mentés!';
    }
}

// Adatok lekérdezése
$termek_id = null;
$termek_nev = null;
$termek_megrendelesek = null;

$termekek = null;
$vevok = null;

if (isset($_GET['termek_id'])) {
    // Termék nevének lekérése
    $termek_id = $_GET['termek_id'];
    $termek = Termek::find($termek_id);
    if ($termek == null) {
        showErrorPage('Nincs ilyen termék!', 404);
    }
} else {
    // Összes termék lekérdezése
    $termekek = Termek::all();
}

// Vevők lekérdezése
$vevok = Vevo::all();
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
                    <a class="nav-link" href="termekek.php">Termékek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vevok.php">Vevők</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="megrendelesek.php">Megrendelések</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">

    <?php if (isset($status)): ?>
        <div class="alert alert-<?= empty($errors) ? 'success' : 'danger' ?>">
            <?= $status ?>
        </div>
    <?php endif; ?>

    <?php if ($termek_id != null): ?>
        <h1>Megrendelések a(z) <?= $termek->nev ?> termékhez</h1>
        <hr class="col-1 my-4">

        <table class="table">
            <tr>
                <th>Vevő</th>
                <th>Darabszám</th>
            </tr>
            <tbody class="table-group-divider">
            <?php if (!$termek->megrendelesek()->exists()): ?>
                <tr>
                    <td colspan="2" class="text-center">Nincs hozzátartozó megrendelés</td>
                </tr>
            <?php else: foreach ($termek->megrendelesek as $megrendeles): ?>
                <tr>
                    <td><?= $megrendeles->vevo->nev ?></td>
                    <td><?= $megrendeles->pivot->db ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h1>Új megrendelés</h1>
    <hr class="col-1 my-4">

    <form method="POST">

        <div class="mb-3">
            <label for="vevo" class="form-label">Vevő</label>
            <select id="vevo" name="vevo" class="form-select  <?= isset($errors['vevo']) ? 'is-invalid' : '' ?>"
                    required>
                <?php foreach ($vevok as $vevo): ?>
                    <option value="<?= $vevo->id ?>"
                        <?php if (isset($megrendeles_vevo) && $megrendeles_vevo == $vevo->id): ?>
                            selected
                        <?php endif; ?>
                    ><?= $vevo->nev ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['vevo'])): ?>
                <div class="invalid-feedback">
                    <?= $errors['vevo'] ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="termek" class="form-label">Termék</label>
            <?php if ($termek_id == null): ?>
                <select id="termek" name="termek"
                        class="form-select  <?= isset($errors['termek']) ? 'is-invalid' : '' ?>"
                        required>
                    <?php foreach ($termekek as $termek): ?>
                        <option value="<?= $termek->id ?>"
                            <?php if (isset($megrendeles_termek) && $megrendeles_termek == $termek->id): ?>
                                selected
                            <?php endif; ?>
                        ><?= $termek->nev ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['termek'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['termek'] ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <input type="hidden" id="termek" name="termek" value="<?= $termek_id ?>">
                <input type="text" class="form-control"
                       value="<?= $termek->nev ?>" readonly disabled>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="db" class="form-label">Darabszám</label>
            <input type="number" id="db" name="db"
                   class="form-control <?= isset($errors['db']) ? 'is-invalid' : '' ?>"
                   value="<?= $megrendeles_db ?? '' ?>"
                   min="1" required>
            <?php if (isset($errors['db'])): ?>
                <div class="invalid-feedback">
                    <?= $errors['db'] ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Megrendelés</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>