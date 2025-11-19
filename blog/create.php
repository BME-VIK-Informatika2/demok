<?php

require_once "lib/helpers.php";
session_start();

$errors = [];

if (!empty($_POST)) {

    if (empty($_POST['title'])) {
        $errors['title'] = 'Title is required.';
    } else {
        $title = $_POST['title'];
    }

    if (isset($_POST['action']) && $_POST['action'] == 'create') {

        if (empty($_POST['content'])) {
            $errors['content'] = 'Content is required.';
        } else {
            $content = $_POST['content'];
        }

        if (empty($errors)) {
            try {
                $db = connectDatabase();

                $stmt = $db->prepare("INSERT INTO posts (title, content) VALUES (:title, :content)");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':content', $content);
                $stmt->execute();

                $_SESSION['message'] = 'Post created successfully.';
                header('Location: index.php');
                exit;

            } catch (PDOException $e) {
                die('Hiba: ' . $e->getMessage());
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'suggest') {

        if (empty($errors)) {
            $ch = curl_init("https://api.mistral.ai/v1/chat/completions");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Authorization: Bearer ' . config('MISTRAL_API_KEY', ''),
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "model" => "open-mistral-7b",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are a blogger and you want to write a new post. You have a title in mind, but 
                                      you are not sure what to write about. You want to generate a short, 1 paragraph 
                                      blog post content without any formating. Just the content, nothing else (including 
                                      title and formating)."
                    ],
                    [
                        "role" => "user",
                        "content" => $title
                    ]
                ],
                "temperature" => 0.5,
                "max_tokens" => 200
            ]));
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                die('CURL Error: ' . curl_error($ch));
            }
            curl_close($ch);
            $suggestion = json_decode($response, true);
            if (isset($suggestion['choices'][0]['message']['content'])) {
                $content = $suggestion['choices'][0]['message']['content'];
            } else {
                $errors['content'] = 'Could not generate content. Please try again.';
            }
        }
    }

}

try {
    $db = connectDatabase();


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
                        <a class="nav-link" href="index.php">Index</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">New Post</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <div class="container">

            <h2 class="my-4">New post</h2>

            <form method="post">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control <?= isset($errors['title']) ? "is-invalid" : "" ?>"
                           id="title" name="title" value="<?= $title ?? '' ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <div class="text-danger mt-1"><?= htmlspecialchars($errors['title']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control <?= isset($errors['content']) ? "is-invalid" : "" ?>" id="content"
                              name="content" rows="5"><?= $content ?? '' ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="text-danger mt-1"><?= htmlspecialchars($errors['content']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="d-flex">
                    <button type="submit" name="action" value="create" class="btn btn-primary">Create</button>
                    <button type="submit" name="action" value="suggest" class="btn btn-link text-decoration-none">
                        Suggest content <i class="bi bi-magic"></i></button>
                </div>

            </form>

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
