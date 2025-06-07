<?php

/** @var \models\Categories[] $categories
 *  
 * 
 */
?>

<h2 class="mb-4 fw-semibold border-bottom pb-2">Доступні категорії</h2>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition-shadow">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title d-flex align-items-center text-primary mb-3">
                            <i class="bi bi-folder2-open me-2 fs-4"></i>
                            <?= htmlspecialchars($category->name) ?>
                        </h5>
                        <p class="card-text text-muted flex-grow-1 fs-6">
                            <?= htmlspecialchars($category->description ?? 'Опис відсутній.') ?>
                        </p>
                        <a href="/topics?category=<?= $category->id ?>" class="btn btn-outline-primary mt-3 align-self-start">
                            Переглянути теми
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>