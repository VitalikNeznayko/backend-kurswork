<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
            <h4 class="mb-4">Адмін панель</h4>
            <?php include 'views/admin/_sidebar.php'; ?>
        </aside>
        <main class="col-md-9 col-lg-10 p-4">
            <h3 class="mb-4">Теми</h3>

            <h5>Додати тему</h5>
            <form id="topic-form" class="mb-4 row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="title" class="form-label">Заголовок</label>
                    <input type="text" id="title" name="title" placeholder="Заголовок" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="content" class="form-label">Вміст</label>
                    <input type="text" id="content" name="content" placeholder="Вміст" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">Категорія</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Оберіть категорію</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->id ?>"><?= htmlspecialchars($category->name) ?></option>
                        <?php endforeach; ?>
                    </select>
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
                        <th>Користувач</th>
                        <th>Категорія</th>
                        <th>Заголовок</th>
                        <th>Вміст</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody id="topics-table">
                    <?php if (!empty($topics)): ?>
                        <?php foreach ($topics as $topic): ?>
                            <tr data-id="<?= $topic->id ?>">
                                <td><?= $topic->id ?></td>
                                <td><?= htmlspecialchars($usersMap[$topic->user_id]->lastName . " " . $usersMap[$topic->user_id]->firstName  ?? 'Невідомий') ?></td>
                                <td>
                                    <select class="form-select editable-select" data-field="category_id">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat->id ?>" <?= $cat->id == $topic->category_id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td contenteditable="true" data-field="title"><?= htmlspecialchars($topic->title) ?></td>
                                <td contenteditable="true" data-field="content"><?= htmlspecialchars($topic->content) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-topic">Видалити</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>
<script>
    const createUrl = '/admin/addTopic';
    const updateUrl = '/admin/editTopic';
    const deleteUrl = '/admin/deleteTopic';


    document.getElementById('topic-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(createUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('message');
                if (data.success) {
                    msg.innerHTML = '<p class="text-success">Тему додано!</p>';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    msg.innerHTML = '<p class="text-danger">' + (data.message || 'Помилка') + '</p>';
                }
            });
    });
    document.querySelectorAll('.editable-select').forEach(select => {
        select.addEventListener('change', function() {
            const tr = select.closest('tr');
            const id = tr.dataset.id;
            const field = select.dataset.field;
            const value = select.value;

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        id,
                        field,
                        value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) alert(data.message || 'Помилка при збереженні');
                });
        });
    });

    document.querySelectorAll('td[contenteditable=true]').forEach(td => {
        td.addEventListener('blur', function() {
            const tr = td.closest('tr');
            const id = tr.dataset.id;
            const field = td.dataset.field;
            const value = td.textContent.trim();

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        id,
                        field,
                        value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) alert(data.message || 'Помилка при збереженні');
                });
        });
    });

    document.querySelectorAll('.delete-topic').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            const id = tr.dataset.id;

            fetch(deleteUrl + '/' + id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        tr.remove();
                    } else {
                        alert(data.message || 'Не вдалося видалити тему');
                    }
                });
        });
    });
</script>