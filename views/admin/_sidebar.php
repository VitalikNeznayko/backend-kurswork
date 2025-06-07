<div class="list-group mb-4">
    <a href="/admin" class="list-group-item list-group-item-action <?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'active' : '' ?>">
        🏠 Головна
    </a>
    <a href="/admin/users" class="list-group-item list-group-item-action <?= ($_SERVER['REQUEST_URI'] === '/admin/users') ? 'active' : '' ?>">
        👤 Користувачі
    </a>
    <a href="/admin/categories" class="list-group-item list-group-item-action <?= ($_SERVER['REQUEST_URI'] === '/admin/categories') ? 'active' : '' ?>">
        🗂 Категорії
    </a>
    <a href="/admin/topics" class="list-group-item list-group-item-action <?= ($_SERVER['REQUEST_URI'] === '/admin/topics') ? 'active' : '' ?>">
        📚 Теми
    </a>
    <a href="/admin/posts" class="list-group-item list-group-item-action <?= ($_SERVER['REQUEST_URI'] === '/admin/posts') ? 'active' : '' ?>">
        📝 Пости
    </a>
    <a href="/admin/comments" class="list-group-item list-group-item-action <?= ($_SERVER['REQUEST_URI'] === '/admin/comments') ? 'active' : '' ?>">
        💬 Коментарі
    </a>
</div>