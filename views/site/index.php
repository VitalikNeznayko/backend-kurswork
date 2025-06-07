<?php

use models\Users;

?>

<div class="bg-white py-5 border-top">
    <div class="container">
        <section class="bg-light rounded-4 py-5 px-5 mb-4 text-center shadow-sm mt-3">
            <h1 class="display-4 fw-bold mb-3">Ласкаво просимо на форум!</h1>
            <p class="lead mb-4 text-secondary">Обговорюйте, діліться, вчіться — разом з нашою спільнотою.</p>
            <?php if (!Users::IsUserLogged()): ?>
                <a href="users/login" class="btn btn-lg btn-primary shadow-sm">
                    <i class="bi bi-person-plus-fill me-2"></i> Приєднатися
                </a>
            <?php endif; ?>
        </section>
        <div class=" mt-4">
            <?php include __DIR__ . '/../category/_list_category.php'; ?>
        </div>
    </div>
</div>