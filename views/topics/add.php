<div class="container mt-4">
    <h2>Створити нову тему</h2>
    <div id="addMessage"></div>
    <form action="/topics/add" id="topicAdd" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок теми</label>
            <input type="text" class="form-control" id="title" name="title">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Зміст теми</label>
            <textarea class="form-control" id="content" name="content" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="categorySelect" class="form-label">Категорія:</label>
            <select class="form-select" id="categorySelect" name="category_id">
                <option value="">Оберіть категорію</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category->id) ?>"
                        <?php if (isset($topic) && $topic->category_id == $category->id): ?> selected <?php endif; ?>>
                        <?= htmlspecialchars($category->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Створити тему</button>
        <a href="/topics" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
<script src="/../public/js/topicAsync.js"></script>