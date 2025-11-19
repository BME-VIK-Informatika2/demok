<?php

require_once "lib/helpers.php";
session_start();

try {
    $db = connectDatabase();

    $posts = $db->query("SELECT * FROM posts")->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    die('Hiba: ' . $e->getMessage());
}

$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-md bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="#">My Lazy Blog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Index</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create.php">New Post</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <div class="container">

            <?php if ($message): ?>
                <div class="alert alert-success mt-4 alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="jumbotron my-4 bg-body-secondary p-3 rounded">
                <h1 class="display-4">Home</h1>
                <p class="lead">Explore how to build a simple AI based application with PHP and Bootstrap, seamlessly
                    integrating Mistral's Completion and DeepL's Translation API to enhance your app with AI-powered
                    suggestion and translation capabilities.</p>
            </div>

            <div class="mb-4">

                <?php if (empty($posts)): ?>
                    <div>No posts</div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="mt-4">
                            <h2><?= htmlspecialchars($post->title) ?></h2>
                            <p><?= htmlspecialchars(substr($post->content, 0, 50)) . " ..." ?></p>
                            <a href="show.php?id=<?= $post->id ?>">Read more</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </main>


    <footer class="py-4 text-center text-body-secondary bg-body-tertiary mt-4">
        Blog demo for <a href="https://portal.vik.bme.hu/kepzes/targyak/VIAUAC10/" class="link-body-emphasis">BMEVIAUAC10</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
</div>
</body>
</html>
