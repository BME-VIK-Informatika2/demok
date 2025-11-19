<?php

require_once "lib/helpers.php";
session_start();

$langs = [
    'DE' => 'Deutsch',
    'FR' => 'Français',
    'ES' => 'Español',
    'IT' => 'Italiano',
    'HU' => 'Magyar',
];

if (isset($_POST['translate'])) {
    if ($_POST['translate'] === 'set' && isset($_POST['lang']) && array_key_exists($_POST['lang'], $langs)) {
        $_SESSION['lang'] = $_POST['lang'];
    } elseif ($_POST['translate'] === 'reset') {
        unset($_SESSION['lang']);
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    die('Missing id parameter');
}

$id = intval($_GET['id']);
try {
    $db = connectDatabase();

    $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        die('Post not found');
    }

    $post = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Hiba: ' . $e->getMessage());
}

$lang = $_SESSION['lang'] ?? 'EN';
if ($lang != 'EN' && array_key_exists($lang, $langs)) {
    $ch = curl_init('https://api-free.deepl.com/v2/translate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Authorization: DeepL-Auth-Key ' . config('DEEPL_API_KEY', ''),
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "source_lang" => "EN",
        "target_lang" => $lang,
        "text" => [
            $post['title'],
            $post['content']
        ],
    ]));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('CURL Error: ' . curl_error($ch));
    }
    curl_close($ch);
    $translations = json_decode($response, true)['translations'];

    $post['title'] = $translations[0]['text'];
    $post['content'] = $translations[1]['text'];
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
                        <a class="nav-link" href="index.php">Index</a>
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

            <div class="row">

                <div class="col-9">
                    <div class="d-flex justify-content-between align-items-center my-4">
                        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i>
                            Back</a>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                            Delete <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <div class="mb-4">
                        <div class="mt-4">
                            <h2><?= htmlspecialchars($post['title']) ?></h2>
                            <p><?= htmlspecialchars($post['content']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="bg-body-secondary px-4 py-2 rounded mt-4">
                        <h4>Translate this post to</h4>
                        <ul>
                            <?php foreach ($langs as $key => $title): ?>
                                <li>
                                    <?php if ($key === $lang): ?>
                                        <form method="post">
                                            <input type="hidden" name="translate" value="reset">
                                            <button type="submit" class="btn btn-link text-decoration-none fw-bold p-0">
                                                <?= $title ?>
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post">
                                            <input type="hidden" name="lang" value="<?= $key ?>">
                                            <input type="hidden" name="translate" value="set">
                                            <button type="submit" class="btn btn-link text-decoration-none p-0">
                                                <?= $title ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteModalLabel">Delete post</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the post?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="delete.php" method="post">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <button type="submit" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <footer class="py-4 text-center text-body-secondary bg-body-tertiary mt-4">
        Blog demo for <a href="https://portal.vik.bme.hu/kepzes/targyak/VIAUAC10/" class="link-body-emphasis">BMEVIAUAC10</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
</div>
</body>
</html>
