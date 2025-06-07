<?php

use models\Users;

/**  @var string $Title*/
/**  @var string $Content*/
/** @var string $avatarPath */
$user = Users::IsUserLogged() ? Users::getCurrentUser() : null;
$avatarPath = Users::GetAvatarPath($user);

if (empty($Title))
    $Title = "";
if (empty($Content))
    $Content = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $Title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/../public/css/style.css">
</head>

<body>
    <header class="p-3 text-bg-dark">
        <div class="container">
            <div class="d-flex flex-row flex-wrap align-items-center justify-content-lg-start">
                <a href="/" class="text-white text-decoration-none hreflogo">
                    <img class="logo" src="/../public/images/site/forumflow.png" alt="forumflow">
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="/" class="nav-link px-2 text-white">Головна сторінка</a></li>
                    <li><a href="/topics" class="nav-link px-2 text-white">Теми обговорення</a></li>
                    <li><a href="/category" class="nav-link px-2 text-white">Категорії</a></li>
                </ul>
                <?php if (!Users::IsUserLogged()) : ?>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary me-2">
                            <a class="nav-link" href="/users/login">Увійти</a>
                        </button>
                        <button type="button" class="btn btn-success">
                            <a class="nav-link" href="/users/register">Зареєструватися</a>
                        </button>
                    </div>
                <?php else : ?>
                    <a href="#" class="d-block text-decoration-none dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src=" <?= $avatarPath ?>" id="avatarHeaderPreview" alt="avatar" class="rounded-circle mr-2"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    </a>
                    <ul class=" dropdown-menu text-small">
                        <li><a class="dropdown-item" href="/posts/add">Створити пост</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/users/profile">Профіль</a></li>
                        <li><a class="dropdown-item" href="/users/logout">Вихід</a></li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class=" container">
        <div class="d-flex justify-content-center"><?= $Content ?></div>
    </main>
    <div class="border-top mt-4"></div>
    <footer class="container d-flex flex-row flex-wrap justify-content-between align-items-center py-3">
        <p class="col-md-4 mb-0 text-body-secondary">© 2025 ForumFlow, Inc</p>
        <a href="/" class="hreflogo">
            <img class="logo" src="/../public/images/site/forumflow.png" alt="forumflow">
        </a>
        <ul class="nav col-md-5  justify-content-end">
            <li class="nav-item"><a href="/" class="nav-link px-2 text-body-secondary">Головна сторінка</a></li>
            <li class="nav-item"><a href="/topics" class="nav-link px-2 text-body-secondary">Теми обговорення</a></li>
            <li class="nav-item"><a href="/category" class="nav-link px-2 text-body-secondary">Категорії</a></li>
        </ul>
    </footer>
</body>

</html>