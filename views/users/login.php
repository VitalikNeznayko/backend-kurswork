<?php

/** @var string $error_message error message to display */
/** @var string $success_message success message to display */

?>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Вхід на сайт</h4>
                </div>
                <div class="card-body">
                    <form method="post" id="loginForm" action="">
                        <div id="loginMessage"></div>

                        <?php if (!empty($success_message)) : ?>
                            <div class="alert alert-success" role="alert">
                                <?= $success_message ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="inputLogin" class="form-label">Email:</label>
                            <input type="text" class="form-control" id="inputLogin" name="login" aria-describedby="loginHelp">
                        </div>

                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Пароль:</label>
                            <input type="password" class="form-control" name="password" id="inputPassword">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Увійти</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Ще не зареєстровані? <a href="/users/register">Зареєструватися</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/../public/js/userAsync.js"></script>