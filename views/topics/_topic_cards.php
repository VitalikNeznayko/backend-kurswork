<?php

/**
 * @var \models\Topics[] $topics
 * @var \models\Categories[] $categories
 */

use models\Users;

$categoriesMap = [];
foreach ($categories as $category) {
    $categoriesMap[$category->id] = $category->name;
}
?>
<?php if (!empty($topics)): ?>
    <?php foreach ($topics as $topic): ?>
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-lg">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between flex-row">
                        <h5 class="card-title mb-0 flex-grow-1">
                            <a href="/topics/view/<?= $topic->id ?>" class="text-decoration-none text-dark stretched-link">
                                <strong class="text-primary"><?= htmlspecialchars($topic->title) ?></strong>
                            </a>
                        </h5>
                        <span class="badge small bg-info text-dark">
                            <?= htmlspecialchars($categoriesMap[$topic->category_id] ?? 'Невідома категорія') ?>
                        </span>
                    </div>
                    <p class="card-text text-muted mb-3 flex-grow-1">
                        <?= nl2br(htmlspecialchars(mb_substr($topic->content, 0, 150))) ?><?php if (mb_strlen($topic->content) > 150) echo '...'; ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top text-sm text-secondary">
                        <div>
                            <?php
                            $author = Users::FindById($topic->user_id);
                            $authorLogin = $author ? htmlspecialchars($author->lastName . " " . $author->firstName) : 'Невідомий';
                            ?>
                            <small><i class="bi bi-person-fill"></i> Автор: <?= $authorLogin ?></small>
                        </div>
                        <div>
                            <small><i class="bi bi-clock-fill"></i> Створено: <?= htmlspecialchars(date('d.m.Y H:i', strtotime($topic->created_at))) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col">
        <div class="alert alert-info text-center py-4 rounded-lg shadow-sm" role="alert">
            <h4 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>Тем у цій категорії немає!</h4>
            <p>Спробуйте обрати іншу категорію або створіть нову тему.</p>
            <?php if (Users::IsUserLogged()): ?>
                <hr>
                <a href="/topics/add" class="btn btn-info mt-2">Створити нову тему</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>