<div class="container mt-4">
    <h2>Додати пост</h2>
    <div id="form-message" class="mb-3 text-danger"></div>

    <form id="add-post-form" method="post">
        <div class="mb-3">
            <label for="topic_id" class="form-label">Оберіть тему:</label>
            <select id="topic_id" name="topic_id" class="form-select">
                <option value="">-- Оберіть тему --</option>
                <?php foreach ($topics as $topic): ?>
                    <option value="<?= $topic->id ?>"
                        <?= (isset($topic_id) && (string)$topic->id === (string)$topic_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($topic->title) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Заголовок поста:</label>
            <input type="text" id="title" name="title" class="form-control">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Вміст:</label>
            <textarea id="content" name="content" rows="6" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Опублікувати</button>
        <a href="/topics" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
<script src="/../public/js/postAsync.js"></script>