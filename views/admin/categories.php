<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
            <h4 class="mb-4">Адмін панель</h4>
            <?php include 'views/admin/_sidebar.php'; ?>
        </aside>
        <main class="col-md-9 col-lg-10 p-4">
            <h3 class="mb-4">Категорії</h3>

            <h5>Додати категорію</h5>
            <form id="category-form" class="mb-4 row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="name" class="form-label">Назва</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Назва" required>
                </div>
                <div class="col-md-5">
                    <label for="description" class="form-label">Опис</label>
                    <input type="text" id="description" name="description" class="form-control" placeholder="Опис" required>
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
                        <th>Назва</th>
                        <th>Опис</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody id="categories-table">
                    <?php if(!empty($categories)):?>
                    <?php foreach ($categories as $category): ?>
                        <tr data-id="<?= $category->id ?>">
                            <td><?= $category->id ?></td>
                            <td contenteditable="true" data-field="name"><?= htmlspecialchars($category->name) ?></td>
                            <td contenteditable="true" data-field="description"><?= htmlspecialchars($category->description) ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-category">Видалити</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif;?>
                </tbody>
            </table>

        </main>
    </div>
</div>

<script>
    const createUrl = '/admin/addCategory';
    const updateUrl = '/admin/editCategory';
    const deleteUrl = '/admin/deleteCategory';

    document.getElementById('category-form').addEventListener('submit', function(e) {
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
                    msg.innerHTML = '<p class="text-success">Категорію додано!</p>';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    msg.innerHTML = '<p class="text-danger">' + (data.message || 'Помилка') + '</p>';
                }
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

    document.querySelectorAll('.delete-category').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            const id = tr.dataset.id;

            fetch(deleteUrl + '/' + id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        tr.remove();
                    } else {
                        alert(data.message || 'Не вдалося видалити категорію');
                    }
                });
        });
    });
</script>