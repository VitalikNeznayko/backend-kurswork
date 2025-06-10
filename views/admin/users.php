<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
            <h4 class="mb-4">Адмін панель</h4>
            <?php include 'views/admin/_sidebar.php'; ?>
        </aside>
        <main class="col-md-9 col-lg-10 p-4">
            <h3 class="mb-4">Користувачі</h3>

            <h5>Додати користувача</h5>
            <form id="user-form" class="mb-4 row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="login" class="form-label">Логін</label>
                    <input type="text" id="login" name="login" class="form-control" placeholder="Логін" required>
                </div>
                <div class="col-md-2">
                    <label for="firstname" class="form-label">Ім'я</label>
                    <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Ім'я" required>
                </div>
                <div class="col-md-2">
                    <label for="lastname" class="form-label">Прізвище</label>
                    <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Прізвище" required>
                </div>
                <div class="col-md-2">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Пароль" required>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="isAdmin" name="isAdmin">
                        <label class="form-check-label" for="isAdmin">
                            Адмін
                        </label>
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-success">Додати</button>
                </div>
            </form>

            <div id="message" class="mb-3"></div>

            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Ім'я</th>
                        <th>Прізвище</th>
                        <th>Адмін</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody id="users-table">
                    <?php foreach ($users as $user): ?>
                        <tr data-id="<?= $user->id ?>">
                            <td><?= $user->id ?></td>
                            <td data-field="login"><?= htmlspecialchars($user->login) ?></td>
                            <td contenteditable="true" data-field="firstname"><?= htmlspecialchars($user->firstName) ?></td>
                            <td contenteditable="true" data-field="lastname"><?= htmlspecialchars($user->lastName) ?></td>
                            <td>
                                <input type="checkbox" class="isAdmin-checkbox" <?= $user->isAdmin ? 'checked' : '' ?>>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-user">Видалити</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>
<script>
    const userForm = document.getElementById("user-form");
    const messageDiv = document.getElementById("message");
    const usersTable = document.getElementById("users-table");

    userForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(userForm);

        fetch("/admin/adduser", {
                method: "POST",
                body: formData,
                credentials: "same-origin",
            })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    messageDiv.textContent = "Користувача додано!";
                    location.reload();
                } else {
                    messageDiv.textContent = data.message || "Помилка при додаванні";
                }
            });
    });

    usersTable.addEventListener(
        "blur",
        (e) => {
            if (e.target.matches('td[contenteditable="true"]')) {
                const td = e.target;
                const tr = td.closest("tr");
                const id = tr.dataset.id;
                const field = td.dataset.field;
                const value = td.textContent.trim();

                fetch("/admin/edituser", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: new URLSearchParams({
                            id,
                            field,
                            value,
                        }),
                        credentials: "same-origin",
                    })
                    .then((res) => res.json())
                    .then((data) => {
                        if (!data.success) alert(data.message || "Помилка при збереженні");
                    });
            }
        },
        true
    );

    usersTable.addEventListener("change", (e) => {
        if (e.target.matches(".isAdmin-checkbox")) {
            const checkbox = e.target;
            const tr = checkbox.closest("tr");
            const id = tr.dataset.id;
            const isAdmin = checkbox.checked ? 1 : 0;

            fetch("/admin/edituser", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        id,
                        field: "isAdmin",
                        value: isAdmin,
                    }),
                    credentials: "same-origin",
                })
                .then((res) => res.json())
                .then((data) => {
                    if (!data.success) alert(data.message || "Помилка при збереженні");
                });
        }
    });

    usersTable.addEventListener("click", (e) => {
        if (e.target.matches(".delete-user")) {
            if (!confirm('Видалити користувача?')) return;
            const tr = e.target.closest("tr");
            const id = tr.dataset.id;

            fetch("/admin/deleteuser/" + id, {
                    method: "POST",
                    credentials: "same-origin",
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        tr.remove();
                        messageDiv.textContent = "Користувача видалено";
                    } else {
                        alert(data.message || "Не вдалося видалити");
                    }
                });
        }
    });
</script>