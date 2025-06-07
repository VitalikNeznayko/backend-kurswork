<?php
use models\Users;
use models\Posts;
?>

<?php if (!empty($posts)): ?>
<div class="list-group">
    <?php foreach ($posts as $post):
        $author = Users::FindById($post->user_id);
        $authorName = $author ? htmlspecialchars($author->lastName . ' ' . $author->firstName) : 'Невідомий користувач';
        $createdAt = Posts::formatTimeAgo($post->created_at);
        $created = new DateTime($post->created_at);
        $now = new DateTime();

        $isNew = ($now->getTimestamp() - $created->getTimestamp()) < 3600;
        $title = $post->title;
    ?>
        <a href="/posts/view/<?= $post->id ?>" class="list-group-item list-group-item-action mb-2 shadow-sm rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0"><?= !empty($title) ? $title : "Без заголовку" ?>
                    <?php if ($isNew): ?>
                        <span class="badge bg-success ms-2">Новий</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="d-flex justify-content-between">
                <small class="text-muted"><?= $createdAt ?></small>
                <small class="text-muted fst-italic">Автор: <?= $authorName ?></small>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php endif;?>