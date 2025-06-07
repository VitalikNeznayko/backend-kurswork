<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
            <h4 class="mb-4">Адмін панель</h4>
            <?php include 'views/admin/_sidebar.php'; ?>
        </aside>
        <main class="col-md-9 col-lg-10 p-4">
            <div class="container-fluid">
                <h3 class="mb-4">Пости</h3>

                <h5>Додати пост</h5>
                <form id="post-form" class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <select name="topic_id" class="form-select" required>
                            <option value="">Оберіть тему</option>
                            <?php foreach ($topics as $topic): ?>
                                <option value="<?= $topic->id ?>"><?= htmlspecialchars($topic->title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Заголовок" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="content" class="form-control" placeholder="Контент" required>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-success">Додати</button>
                    </div>
                </form>


                <div id="post-message" class="mb-3"></div>

                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Тема</th>
                            <th>Користувач</th>
                            <th>Заголовок</th>
                            <th>Контент</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody id="posts-table">
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <tr data-id="<?= $post->id ?>">
                                    <td><?= $post->id ?></td>
                                    <td>
                                        <select class="form-select change-topic" data-id="<?= $post->id ?>">
                                            <?php foreach ($topics as $topic): ?>
                                                <option value="<?= $topic->id ?>" <?= $topic->id == $post->topic_id ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($topic->title) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <td><?= htmlspecialchars($usersMap[$post->user_id]->lastName . " " . $usersMap[$post->user_id]->firstName  ?? 'Невідомий') ?></td>
                                    <td contenteditable="true" data-field="title"><?= htmlspecialchars($post->title) ?></td>
                                    <td contenteditable="true" data-field="content"><?= htmlspecialchars($post->content) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger delete-post">Видалити</button>
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
    const createUrl = '/admin/addPost';
    const updateUrl = '/admin/editPost';
    const deleteUrl = '/admin/deletePost';

    document.getElementById('post-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(createUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('post-message');
                if (data.success) {
                    msg.innerHTML = '<p class="text-success">Пост додано!</p>';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    msg.innerHTML = '<p class="text-danger">' + (data.message || 'Помилка') + '</p>';
                }
            });
    });
    document.querySelectorAll('.change-topic').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.dataset.id;
            const topicId = this.value;

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        id,
                        field: 'topic_id',
                        value: topicId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) alert(data.message || 'Помилка при оновленні теми');
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
                    if (!data.success) alert(data.message || 'Помилка при редагуванні');
                });
        });
    });

    document.querySelectorAll('.delete-post').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Видалити пост?')) return;

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
                        alert(data.message || 'Не вдалося видалити пост');
                    }
                });
        });
    });
</script>