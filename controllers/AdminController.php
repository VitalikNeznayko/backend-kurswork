<?php

namespace controllers;

use core\Controller;
use Dom\Comment;
use models\Categories;
use models\Comments;
use models\Posts;
use models\Topics;
use models\Users;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $user = Users::getCurrentUser();
        if (!$user || !$user->isAdmin) {
            http_response_code(403);
            $this->redirect('/'); 
        }
    }
    public function actionIndex()
    {
        $this->template->Title = "Адмін панель";
        $categoriesCount = Categories::countAll();
        $topicsCount = Topics::countAll();
        $postsCount = Posts::countAll();
        $userCount = Users::countAll();
        $categoriesCount = Categories::countAll();

        $this->template->setParam('categoriesCount', $categoriesCount);
        $this->template->setParam('userCount', $userCount);
        $this->template->setParam('topicsCount', $topicsCount);
        $this->template->setParam('postsCount', $postsCount);

        return $this->render();
    }

    public function actionUsers()
    {
        $this->template->Title = "Таблиця користувачів";
        $users = Users::findByCondition([]);
        $this->template->setParam('users', $users);
        return $this->render();
    }
    public function actionAddUser()
    {
        if ($this->isPost) {
            $login = $this->post->login ?? '';
            $firstname = $this->post->firstname ?? ' ';
            $lastname = $this->post->lastname ?? '';
            $password = $this->post->password ?? ' ';
            $isAdmin = isset($this->post->isAdmin) ? 1 : 0;

            if ($login && $firstname && $lastname && $password) {
                $existing = Users::FindByLogin($login);
                if ($existing) {
                    return $this->respondJson(['success' => false, 'message' => 'Логін вже зайнятий']);
                }
                Users::RegisterUser($login, password_hash($password, PASSWORD_DEFAULT), $lastname, $firstname, $isAdmin);

                return $this->respondJson(['success' => true]);
            }
            return $this->respondJson(['success' => false, 'message' => 'Заповніть всі поля']);
        }
        return $this->respondJson(['success' => false, 'message' => 'Невірний метод']);
    }
    public function actionEditUser()
    {
        if ($this->isPost) {
            $id = $_POST['id'] ?? null;
            $field = $_POST['field'] ?? null;
            $value = $_POST['value'] ?? null;

            $allowedFields = ['login', 'firstname', 'lastname', 'isAdmin'];
            if (!$id || !$field || !in_array($field, $allowedFields)) {
                return $this->respondJson(['success' => false, 'message' => 'Невірні параметри']);
            }

            $user = Users::FindById($id);
            if (!$user) {
                return $this->respondJson(['success' => false, 'message' => 'Користувача не знайдено']);
            }

            if ($field === 'isAdmin') {
                $user->isAdmin = (int)$value === 1 ? 1 : 0;
            } else {
                $user->$field = trim($value);
            }

            $user->save();
            return $this->respondJson(['success' => true]);
        }
        return $this->respondJson(['success' => false, 'message' => 'Невірний метод']);
    }
    public function actionDeleteUser($params)
    {
        $id = $params[0] ?? null;
        $currentUserId = Users::getCurrentUser()->id;

        if ($id && $id != $currentUserId) {
            $user = Users::FindById($id);
            if ($user) {
                Users::deleteById($id);
                return $this->respondJson(['success' => true]);
            }
        }
        return $this->respondJson(['success' => false, 'message' => 'Не вдалося видалити']);
    }
    public function actionTopics()
    {
        $this->template->Title = "Таблиця тем";
        $users = Users::findByCondition([]);
        $categories = Categories::getAllCategories();

        $usersMap = [];
        foreach ($users as $u) {
            $usersMap[$u->id] = $u;
        }
        $this->template->setParam('usersMap',   $usersMap);
        $this->template->setParam('categories', $categories);
        $topics = Topics::findByCondition([]);
        $this->template->setParam('topics', $topics);
        return $this->render();
    }
    public function actionAddTopic()
    {
        if ($this->isPost) {
            $title = trim($this->post->title ?? '');
            $content = trim($this->post->content ?? '');
            $userid = Users::getCurrentUser()->id;
            $categoryId  = intval($this->post->category_id ?? 0);

            if ($title && $content) {   
                Topics::createTopic($userid, $title, $content, $categoryId);

                $this->respondJson(['success' => true]);
            } else {
                $this->respondJson(['success' => false, 'message' => 'Заповніть всі поля']);
            }
        }
    }
    public function actionEditTopic()
    {
        if ($this->isPost) {
            $id = $this->post->id ?? null;
            $field = $this->post->field ?? null;
            $value = $this->post->value ?? null;

            $allowedFields = ['title', 'content', 'user_id', 'category_id'];

            if (!$id || !$field || !in_array($field, $allowedFields)) {
                $this->respondJson(['success' => false, 'message' => 'Невірні параметри']);
            }

            $topic = Topics::FindById($id);
            if (!$topic) {
                $this->respondJson(['success' => false, 'message' => 'Тему не знайдено']);
            }
            $topic->$field = trim($value);
            $topic->save();

            $this->respondJson(['success' => true]);
        }
    }
    public function actionDeleteTopic($params)
    {
        $id = $params[0] ?? null;
        if ($id) {
            $topic = Topics::FindById($id);
            if ($topic) {
                Topics::deleteById($id);
                $this->respondJson(['success' => true]);
            }
        }
        $this->respondJson(['success' => false, 'message' => 'Не вдалося видалити тему']);
    }
    public function actionPosts()
    {
        $this->template->Title = "Таблиця постів";
        $posts = Posts::findByCondition([]);
        $topics = Topics::findByCondition([]);
        $users = Users::findByCondition([]);


        $usersMap = [];
        foreach ($users as $u) {
            $usersMap[$u->id] = $u;
        }

        $this->template->setParams([
            'posts' => $posts,
            'topicsMap' => $topics,
            'usersMap' => $usersMap,
            'topics' => $topics,
            'users' => $users,
        ]);
        return $this->render();
    }

    public function actionAddPost()
    {
        if ($this->isPost) {
            $topicId = $_POST['topic_id'] ?? null;
            $userId = Users::getCurrentUser()->id;
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if ($topicId && $userId && $title && $content) {
                Posts::createPost($topicId, $userId, $title, $content);
                $this->respondJson(['success' => true]);
            } else {
                $this->respondJson(['success' => false, 'message' => 'Заповніть всі поля']);
            }
        }
    }

    public function actionEditPost()
    {
        if ($this->isPost) {
            $id = $_POST['id'] ?? null;
            $field = $_POST['field'] ?? null;
            $value = trim($_POST['value'] ?? '');

            $allowedFields = ['title', 'content', 'topic_id'];
            if (!$id || !in_array($field, $allowedFields)) {
                $this->respondJson(['success' => false, 'message' => 'Невірні параметри']);
            }

            $post = Posts::FindById($id);
            if (!$post) {
                $this->respondJson(['success' => false, 'message' => 'Пост не знайдено']);
            }

            $post->$field = $value;
            $post->save();
            $this->respondJson(['success' => true]);
        }
    }

    public function actionDeletePost($params)
    {
        $id = $params[0] ?? null;
        if ($id) {
            $post = Posts::FindById($id);
            if ($post) {
                Posts::deleteById($id);
                $this->respondJson(['success' => true]);
            }
        }
        $this->respondJson(['success' => false, 'message' => 'Не вдалося видалити пост']);
    }

    public function actionCategories()
    {
        $this->template->Title = "Таблиця категорій";
        $categories = Categories::getAllCategories();
        $this->template->setParam('categories', $categories);
        return $this->render();
    }

    public function actionAddCategory()
    {
        if ($this->isPost) {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($name && $description) {
                Categories::createCategory($name, $description);
                $this->respondJson(['success' => true]);
            } else {
                $this->respondJson(['success' => false, 'message' => 'Заповніть всі поля']);
            }
        }
    }

    public function actionEditCategory()
    {
        if ($this->isPost) {
            $id = $_POST['id'] ?? null;
            $field = $_POST['field'] ?? null;
            $value = $_POST['value'] ?? null;

            $allowedFields = ['name', 'description'];
            if (!$id || !$field || !in_array($field, $allowedFields)) {
                $this->respondJson(['success' => false, 'message' => 'Невірні параметри']);
            }

            $category = Categories::FindById($id);
            if (!$category) {
                $this->respondJson(['success' => false, 'message' => 'Категорію не знайдено']);
            }

            $category->$field = trim($value);
            $category->save();

            $this->respondJson(['success' => true]);
        }
    }

    public function actionDeleteCategory($params)
    {
        $id = $params[0] ?? null;
        if ($id) {
            $category = Categories::FindById($id);
            if ($category) {
                Categories::deleteById($id);
                $this->respondJson(['success' => true]);
            }
        }
        $this->respondJson(['success' => false, 'message' => 'Не вдалося видалити категорію']);
    }
    public function actionComments()
    {
        $this->template->Title = "Таблиця коментів";
        $comments = Comments::findByCondition([]);
        $users = Users::findByCondition([]);
        $posts = Posts::findByCondition([]);

        $usersMap = [];
        foreach ($users as $user) {
            $usersMap[$user->id] = $user;
        }


        $this->template->setParams([
            'comments' => $comments,
            'usersMap' => $usersMap,
            'posts' => $posts
        ]);

        return $this->render();
    }

    public function actionEditComment()
    {
        if ($this->isPost) {
            $id = $_POST['id'] ?? null;
            $field = $_POST['field'] ?? null;
            $value = $_POST['value'] ?? null;

            $allowedFields = ['post_id', 'content'];
            if (!$id || !$field || !in_array($field, $allowedFields)) {
                $this->respondJson(['success' => false, 'message' => 'Невірні параметри']);
            }

            $comment = Comments::FindById($id);
            if (!$comment) {
                $this->respondJson(['success' => false, 'message' => 'Коментарь не знайдено']);
            }

            $comment->$field = trim($value);
            $comment->save();

            $this->respondJson(['success' => true]);
        }
    }

    public function actionAddComment()
    {
        if ($this->isPost) {
            $postId = $_POST['post_id'] ?? null;
            $userId = Users::getCurrentUser()->id ?? null;
            $content = trim($_POST['content'] ?? '');

            if ($postId && $userId && $content) {
                Comments::createComment($postId, null, $userId, $content);
                return $this->respondJson(['success' => true]);
            } else {
                return $this->respondJson(['success' => false, 'message' => 'Заповніть всі поля']);
            }
        }
        return $this->respondJson(['success' => false, 'message' => 'Невірний метод']);
    }

    public function actionDeleteComment($params)
    {
        $id = $params[0] ?? null;
        if (!$id) {
            return $this->respondJson(['success' => false, 'message' => 'Відсутній ID коментаря']);
        }

        $comment = Comments::FindById($id);
        if (!$comment) {
            return $this->respondJson(['success' => false, 'message' => 'Коментар не знайдено']);
        }

        Comments::deleteById($id);

        return $this->respondJson(['success' => true]);
    }
}