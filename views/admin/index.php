<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
            <h4 class="mb-4">Адмін панель</h4>
            <?php include 'views/admin/_sidebar.php'; ?>
        </aside>
        <main class="col-md-9 col-lg-9 p-4">
            <h2>Головна</h2>
            <div class="row g-4 mt-3">

                <div class="col-md-4">
                    <div class="card text-bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Користувачі</h5>
                            <p class="card-text display-5"><?= $userCount ?? 0 ?></p>
                            <a href="/admin/users" class="btn btn-light btn-sm">Керувати користувачами</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title text-white">Категорії</h5>
                            <p class="card-text display-5 text-white"><?= $categoriesCount ?? 0 ?></p>
                            <a href="/admin/categories" class="btn btn-light btn-sm">Керувати категоріями</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Теми</h5>
                            <p class="card-text display-5"><?= $topicsCount ?? 0 ?></p>
                            <a href="/admin/topics" class="btn btn-light btn-sm">Керувати темами</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title text-white">Пости</h5>
                            <p class="card-text display-5 text-white"><?= $postsCount ?? 0 ?></p>
                            <a href="/admin/posts" class="btn btn-light btn-sm">Керувати постами</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Коментарі</h5>
                            <p class="card-text display-5"><?= $categoriesCount ?? 0 ?></p>
                            <a href="/admin/comments" class="btn btn-light btn-sm">Керувати коментарями</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>