<?php

use models\Users;
use models\Topics;

/**
 * @var \models\Posts      $post
 * @var \models\Comments[] $comments
 */
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
function renderComments(array $comments, $currentUser, $parentId = null)
{
    foreach ($comments as $comment) {
        if ($comment->parent_id === $parentId) {
            $commentAuthor = Users::FindById($comment->user_id);
            $commentAuthorName = $commentAuthor
                ? htmlspecialchars($commentAuthor->lastName . ' ' . $commentAuthor->firstName)
                : 'Гість';
?>
            <div class="col" id="comment-<?= $comment->id ?>" style="margin-left: <?= $parentId ? '30px' : '0' ?>; margin-top:15px">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong><?= $commentAuthorName ?></strong>
                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($comment->created_at)) ?></small>
                    </div>
                    <div class="card-body">
                        <p class="card-text comment-content"><?= nl2br(htmlspecialchars($comment->content)) ?></p>
                        <?php if ($currentUser && $currentUser->id === $comment->user_id): ?>
                            <div class="edit-comment-form-container mt-2" style="display: none;">
                                <textarea class="form-control edit-comment-textarea" rows="3"><?= htmlspecialchars($comment->content) ?></textarea>
                                <button class="btn btn-success btn-sm mt-2 save-comment-btn" data-comment-id="<?= $comment->id ?>">Зберегти</button>
                                <button class="btn btn-secondary btn-sm mt-2 cancel-edit-comment-btn">Скасувати</button>
                                <div class="edit-comment-error-message text-danger mt-2"></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($currentUser): ?>
                            <button type="button" class="btn btn-link btn-sm reply-comment-btn" data-comment-id="<?= $comment->id ?>">
                                Відповісти
                            </button>

                            <div class="reply-comment-form-container mt-2" style="display:none;">
                                <textarea class="form-control reply-comment-textarea" rows="3" placeholder="Ваша відповідь..."></textarea>
                                <button class="btn btn-primary btn-sm mt-2 submit-reply-btn" data-parent-id="<?= $comment->id ?>">Відправити</button>
                                <button class="btn btn-secondary btn-sm mt-2 cancel-reply-btn">Скасувати</button>
                                <div class="reply-comment-error-message text-danger mt-2"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($currentUser && $currentUser->id === $comment->user_id): ?>
                        <div class="card-footer text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-comment-btn" data-comment-id="<?= $comment->id ?>">
                                Редагувати
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="<?= $comment->id ?>">
                                Видалити
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                renderComments($comments, $currentUser, $comment->id);
                ?>
            </div>
<?php
        }
    }
}

$author = Users::FindById($post->user_id);
$authorName = $author
    ? htmlspecialchars($author->lastName . ' ' . $author->firstName)
    : 'Невідомий';
$currentUser = Users::getCurrentUser();
$topic = Topics::FindById($post->topic_id);
?>

<div class="container mt-4">
    <?php if ($post): ?>
        <?php if ($topic): ?>
            <h5>
                <a href="/topics/view/<?= $topic->id ?>" class="text-decoration-none text-secondary">
                    🔙Тема: <?= htmlspecialchars($topic->title) ?>
                </a>
            </h5>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
            <h2 id="postTitle" class="mb-0"><?= htmlspecialchars($post->title) ?></h2>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
            <div>
                Автор: <?= $authorName ?> |
                Опубліковано: <?= date('d.m.Y H:i', strtotime($post->created_at)) ?>
            </div>

            <?php if ($currentUser && $currentUser->id === $post->user_id): ?>
                <div class="dropdown">
                    <a href="#" class="text-decoration-none text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        •••
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small">
                        <li>
                            <a id="editPostBtn"
                                class="dropdown-item"
                                data-post-id="<?= $post->id ?>">
                                Редагувати пост
                            </a>
                        </li>
                        <li>
                            <a href="/posts/delete/<?= $post->id ?>"
                                class="dropdown-item"
                                onclick="return confirm('Ви впевнені, що хочете видалити цей пост?');">
                                Видалити пост
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="card mb-4 shadow-sm border-0 rounded-lg">
            <div class="card-body">
                <p id="postContent" class="card-text"><?= nl2br(htmlspecialchars($post->content)) ?></p>
            </div>
        </div>

        <?php if (Users::IsUserLogged()): ?>
            <h5 class="mb-3">Додати новий коментар</h5>
            <form id="add-comment-form">
                <input type="hidden" name="post_id" value="<?= $post->id ?>">
                <div class="mb-3">
                    <textarea name="content"
                        id="commentContent"
                        rows="3"
                        class="form-control"
                        placeholder="Ваш коментар..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Відправити</button>
            </form>
            <div id="add-comment-error" class="text-danger mt-2"></div>
        <?php else: ?>
            <p>Щоб додати коментар, будь ласка, <a href="/users/login">увійдіть у систему</a>.</p>
        <?php endif; ?>

        <h4 class="mb-3 mt-4">Коментарі</h4>

        <div class="row row-cols-1 g-3 mb-4" id="comments-list">
            <?php if (!empty($comments)): ?>
                <?php renderComments($comments,  $currentUser, null); ?>
            <?php else: ?>
                <div class="col">
                    <p class="text-muted fst-italic mb-4" id="no-comments-message">Ще немає коментарів.</p>
                </div>
            <?php endif; ?>
        </div>


    <?php else: ?>
        <div class="alert alert-warning">Пост не знайдено.</div>
        <a href="/topics" class="btn btn-secondary">Повернутися до списку тем</a>
    <?php endif; ?>
</div>
<script src="/../public/js/commentAsync.js"></script>
<script src="/../public/js/postAsync.js"></script>