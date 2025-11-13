<?php
if (!isset($errorCode) || !isset($errorTitle) || !isset($errorMessage)) {
    header('Location: ../index.php');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Webshop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
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
                    <a class="nav-link" href="megrendelesek.php">Megrendelések</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h1>Hiba történt!</h1>
    <h2><?= $errorCode ?> - <?= $errorTitle ?></h2>
    <div class="col-lg-8 px-0">
        <p class="fs-5"><?= $errorMessage ?></p>
        <hr class="col-1 my-4">

        <a href="index.php">Vissza a főoldalra</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>