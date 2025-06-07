<?php

/** @var string $error_message */
?>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Зареєструватися</h4>
                </div>
                <div class="card-body">
                    <div id="registerMessage"></div>
                    <form method="post" id="registerForm" action="">

                        <div class="mb-3">
                            <label for="inputLogin" class="form-label">Email:</label>
                            <input type="text" class="form-control" id="inputLogin" name="login">
                        </div>

                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Пароль:</label>
                            <input type="password" class="form-control" name="password" id="inputPassword">
                        </div>

                        <div class="mb-3">
                            <label for="inputRepeatPassword" class="form-label">Повторіть пароль:</label>
                            <input type="password" class="form-control" name="repeatPassword" id="inputRepeatPassword">
                        </div>

                        <div class="mb-3">
                            <label for="inputFirstname" class="form-label">Ім'я:</label>
                            <input type="text" class="form-control" name="firstname" id="inputFirstname">
                        </div>

                        <div class="mb-3">
                            <label for="inputLastname" class="form-label">Прізвище:</label>
                            <input type="text" class="form-control" name="lastname" id="inputLastname">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Зареєструватися</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Вже зареєстровані? <a href="/users/login">Увійти</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/../public/js/userAsync.js"></script>