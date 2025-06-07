<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
            <h4 class="mb-4">Адмін панель</h4>
            <?php include 'views/admin/_sidebar.php'; ?>
        </aside>
        <main class="col-md-9 col-lg-10 p-4">
            <div class="container-fluid">
                <h3 class="mb-4">Коментарі</h3>

                <h5>Додати коментар</h5>
                <form id="comment-form" class="row g-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <select name="post_id" class="form-select" required>
                            <option value="">Оберіть пост</option>
                            <?php foreach ($posts as $post): ?>
                                <option value="<?= $post->id ?>"><?= htmlspecialchars($post->title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="content" class="form-control" placeholder="Текст коментаря" required>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-success">Додати</button>
                    </div>
                </form>

                <div id="comment-message" class="mb-3"></div>

                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Пост</th>
                            <th>Користувач</th>
                            <th>Контент</th>
                            <th>Дата</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody id="comments-table">
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                                <tr data-id="<?= $comment->id ?>">
                                    <td><?= $comment->id ?></td>
                                    <td>
                                        <select class="form-select change-post" data-id="<?= $comment->id ?>">
                                            <?php foreach ($posts as $post): ?>
                                                <option value="<?= $post->id ?>"
                                                    <?= $post->id == $comment->post_id ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($post->title) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($usersMap[$comment->user_id]->lastName . ' ' . $usersMap[$comment->user_id]->firstName ?? 'Невідомий користувач') ?>
                                    </td>
                                    <td contenteditable="true" data-field="content"><?= htmlspecialchars($comment->content) ?></td>
                                    <td><?= htmlspecialchars($comment->created_at) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger delete-comment">Видалити</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </main>
    </div>
</div>

<script>
    const updateUrl = '/admin/editComment';
    const deleteUrl = '/admin/deleteComment';
    const addCommentUrl = '/admin/addComment';

    document.querySelectorAll('.change-post').forEach(select => {
        select.addEventListener('change', () => {
            const id = select.dataset.id;
            const value = select.value;

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        id,
                        field: 'post_id',
                        value
                    })
                })
                .then(r => r.json())
                .then(j => {
                    if (!j.success) alert(j.message || 'Помилка при оновленні поста');
                });
        });
    });

    document.querySelectorAll('td[contenteditable][data-field="content"]').forEach(td => {
        td.addEventListener('blur', () => {
            const tr = td.closest('tr');
            const id = tr.dataset.id;
            const value = td.textContent.trim();

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        id,
                        field: 'content',
                        value
                    })
                })
                .then(r => r.json())
                .then(j => {
                    if (!j.success) alert(j.message || 'Помилка при редагуванні контенту');
                });
        });
    });

    document.querySelectorAll('.delete-comment').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!confirm('Видалити коментар?')) return;
            const tr = btn.closest('tr');
            const id = tr.dataset.id;

            fetch(`${deleteUrl}/${id}`, {
                    method: 'POST'
                })
                .then(r => r.json())
                .then(j => {
                    if (j.success) {
                        tr.remove();
                    } else {
                        alert(j.message || 'Не вдалося видалити коментар');
                    }
                });
        });
    });

    document.getElementById('comment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(addCommentUrl, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                const msg = document.getElementById('comment-message');
                if (data.success) {
                    msg.innerHTML = '<p class="text-success">Коментар додано!</p>';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    msg.innerHTML = '<p class="text-danger">' + (data.message || 'Помилка') + '</p>';
                }
            });
    });
</script>