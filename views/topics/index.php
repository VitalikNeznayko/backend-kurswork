<?php

use models\Categories;
use models\Users;

include 'views/sort/_sort.php';
/**
 * @var \core\Template $template
 * @var \models\Topics[] $topics
 * @var \models\Categories[] $categories
 */

$sort = $_GET['sort'] ?? '';
$currentGet = $_GET;

$categoryName = 'Невідома категорія';

?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            Актуальні теми
            <?php if (isset($_GET['category'])): ?>
                <?php
                $category = Categories::findById($_GET['category']);
                ?>
                за категорією "<?= $category->name ?>"
            <?php endif; ?>
        </h2>

        <?php if (Users::IsUserLogged()): ?>
            <a href="/topics/add" class="btn btn-primary btn-lg shadow-sm">
                <i class="bi bi-plus-circle me-2"></i> Створити нову тему
            </a>
        <?php endif; ?>
    </div>

    <div id="sortSelect" class="btn-group mb-2" role="group" aria-label="Сортування">
        <?php
        echo sortLink('Новіші', 'date_desc', $currentGet, "/topics?");
        echo sortLink('Старіші', 'date_asc', $currentGet, "/topics?");
        echo sortLink('А-Я', 'title_asc', $currentGet, "/topics?");
        echo sortLink('Я-А', 'title_desc', $currentGet, "/topics?");
        ?>
    </div>

    <?php if (!empty($topics)): ?>
        <div id="topicsContainer" class="row row-cols-1 g-4">
            <?php include 'views/topics/_topic_cards.php'; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-4 rounded-lg shadow-sm" role="alert">
            <h4 class="alert-heading">
                <i class="bi bi-info-circle-fill me-2"></i>Наразі тем немає!
            </h4>
            <p>Будьте першим, хто створить нову дискусію.</p>

            <?php if (Users::IsUserLogged()): ?>
                <hr>
                <a href="/topics/add" class="btn btn-info mt-2">Створити першу тему</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<script src="/../public/js/sortAsync.js"></script>