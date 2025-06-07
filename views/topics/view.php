<?php

use models\Users;

include __DIR__ . '/../sort/_sort.php';
/**
 * @var \models\Topics $topic
 * @var \models\Posts[] $posts
 */

$sort = $_GET['sort'] ?? '';
$currentGet = $_GET;

$categoryName = 'Невідома категорія';
if ($topic && isset($categories)) {
    foreach ($categories as $category) {
        if ($category['id'] == $topic->category_id) {
            $categoryName = $category['name'];
            break;
        }
    }
}
?>

<div class="container mt-4">
    <?php if ($topic): ?>
        <h2 id="mainTitle"><?= htmlspecialchars($topic->title) ?></h2>
        <div class="d-flex justify-content-between flex-row align-items-center">
            <div class="text-muted">
                <span class="badge bg-info text-dark"><?= htmlspecialchars($categoryName) ?></span>
                Автор: <?= htmlspecialchars(Users::FindById($topic->user_id)->lastName . " " . Users::FindById($topic->user_id)->firstName) ?? "Невідомий" ?> |
                Створено: <?= htmlspecialchars($topic->created_at) ?>
            </div>
            <?php if (Users::IsUserLogged() && Users::getCurrentUser()->id === $topic->user_id): ?>
                <a href="#" class="text-decoration-none text-black" data-bs-toggle="dropdown" aria-expanded="false">•••</a>
                <ul class="dropdown-menu text-small">
                    <li>
                        <a id="editTopicBtn" class="dropdown-item"
                            data-categories="<?= htmlspecialchars(json_encode($categories)) ?>"
                            data-topic-id="<?= $topic->id ?>">Редагувати тему</a>
                    </li>
                    <li><a href="/topics/delete/<?= $topic->id ?>" class="dropdown-item"
                            onclick="return confirm('Ви впевнені, що хочете видалити цю тему?');">Видалити тему</a></li>
                </ul>
            <?php endif; ?>
        </div>
        <hr>
        <div class="card mb-4">
            <div class="card-body">
                <p id="mainContent" class="card-text"><?= nl2br(htmlspecialchars($topic->content)) ?></p>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-between">
            <a href="/topics" class="btn btn-secondary mb-4">Перейти до списку тем</a>
            <a href="/topics?category=<?= $topic->category_id ?>" id="backCategory" class="btn btn-secondary mb-4">Перейти до категорії "<?= $categoryName ?>"</a>

        </div>
        <div class="d-flex justify-content-between flex-row align-items-center">
            <h3 class="mb-4">Пости <?= isset($posts) ? "(" . count($posts) . ")" : "" ?></h3>
            <?php if (Users::IsUserLogged()): ?>
                <a href="/posts/add" class="btn btn-primary shadow-sm">
                    <i class=" bi-plus-circle me-1"></i> Створити новий пост
                </a>
            <?php endif; ?>
        </div>
        <?php if ($posts): ?>
            <div>
                <div id="sortSelect" class="btn-group mb-2" role="group" aria-label="Сортування">
                    <?php
                    echo sortLink('Новіші', 'date_desc', $currentGet, "/topics/view/" . $topic->id . "?");
                    echo sortLink('Старіші', 'date_asc', $currentGet, "/topics/view/" . $topic->id . "?");
                    echo sortLink('А-Я', 'title_asc', $currentGet, "/topics/view/" . $topic->id . "?");
                    echo sortLink('Я-А', 'title_desc', $currentGet, "/topics/view/" . $topic->id . "?");
                    ?>
                </div>
            </div>
            <div id="topicsContainer">

                <?php include __DIR__ . '/../posts/_list_posts.php'; ?>
            </div>
        <?php else: ?>
            <p class="text-muted fst-italic">Поки що немає постів в цій темі.</p>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning">Тему не знайдено.</div>
        <a href="/topics" class="btn btn-secondary">Повернутися до списку тем</a>
    <?php endif; ?>
</div>
<script src="/../public/js/topicAsync.js"></script>
<script src="/../public/js/sortAsync.js"></script>