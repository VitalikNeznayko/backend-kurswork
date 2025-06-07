<?php

/** @var object $user */
/** @var string $avatarPath */
$isAdmin = $user->isAdmin ?? false;
?>
<?php if (isset($user)): ?>
    <div class="container mt-3">
        <div id="avatarMessage"></div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center flex-row">
                        <img src="<?= $avatarPath ?>" alt="Аватар" id="avatarPreview" class="rounded-circle mr-3"
                            style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                        <p class="h5 mb-0">Вітаємо, <?= htmlspecialchars($user->firstName ?? '—') ?> <?= htmlspecialchars($user->lastName ?? '—') ?>!</p>
                    </div>
                    <div class="d-flex text-end align-items-center">
                        <form action="/users/profile" method="post" enctype="multipart/form-data" id="avatarForm">
                            <div class="d-flex align-items-center flex-row">
                                <input type="file" name="photo" id="photo" style="margin-right: 10px" class="form-control">
                                <button type="submit" class="btn btn-primary">Зберегти фото</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Особиста інформація</h5>
                    </div>
                    <div class="card-body" id="profileInfo">
                        <div id="editMessage"></div>
                        <div class="mb-3">
                            <p class="mb-2 info-field">
                                <strong>Ім'я:</strong>
                                <span class="text-muted" data-field="firstname"><?= htmlspecialchars($user->firstName ?? '—') ?></span>
                            </p>
                            <p class="mb-2 info-field">
                                <strong>Прізвище:</strong>
                                <span class="text-muted" data-field="lastname"><?= htmlspecialchars($user->lastName ?? '—') ?></span>
                            </p>
                            <p class="mb-2 info-field">
                                <strong>Email:</strong>
                                <span class="text-muted" data-field="login"><?= htmlspecialchars($user->login ?? '—') ?></span>
                            </p>
                            <p class="mb-2 password-field" style="display: none;">
                                <strong>Новий пароль:</strong>
                                <span class="text-muted" data-field="password"></span>
                            </p>
                            <p class="mb-2 password-field" style="display: none;">
                                <strong>Повторіть пароль:</strong>
                                <span class="text-muted" data-field="repeatPassword"></span>
                            </p>
                        </div>
                        <button id="editProfileBtn" class="btn btn-outline-primary">Редагувати профіль</button>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?php if ($isAdmin): ?>
                    <a href="/admin" class="btn btn-warning float-right">Адмін панель</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-3">
            <a href="/users/logout" class="btn btn-danger">Вийти з аккаунту</a>
        </div>
    </div>
<?php else: ?>
    <div class="container mt-4">
        <p>Інформація про користувача не знайдена.</p>
    </div>
<?php endif; ?>
<script src="/../public/js/userAsync.js"></script>