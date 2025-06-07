<?php

namespace controllers;

use core\Controller;
use models\Categories;
use models\Topics;
use models\Users;
use models\Posts;

class TopicsController extends Controller
{
    public function actionIndex()
    {
        $category = $this->get->category;
        $sort = $this->get->sort;

        $sortMap = [
            'date_desc' => ['created_at' => 'DESC'],
            'date_asc' => ['created_at' => 'ASC'],
            'title_asc' => ['title' => 'ASC'],
            'title_desc' => ['title' => 'DESC'],
        ];
        $order = isset($sortMap[$sort]) ? $sortMap[$sort] : ['created_at' => 'DESC'];

        if ($category !== null && $category !== '') {
            $categoryId = (int) $category;
            $topics = Topics::findByCondition(['category_id' => $categoryId], $order);
        } else {
            $topics = Topics::findAll($order);
        }

        $categories = Categories::findAll();

        if ($this->isAjax()) {
            $this->template->setParam('topics', $topics);
            $this->template->setParam('categories', $categories);
            echo $this->template->renderPartial('topics/_topic_cards');
            exit;
        }

        $this->template->Title = "Актуальні теми";
        $this->template->setParam('topics', $topics);
        $this->template->setParam('categories', $categories);
        return $this->render();
    }
    public function actionView($params)
    {
        $id = $params[0] ?? null;
        if (!$id) return $this->isAjax()
            ? $this->respondJson(['status' => 'error', 'message' => 'Теми з таким id не існує', 'redirect' => '/topics']) : $this->redirect("/topics");

        $topic = Topics::FindById($id);
        if (!$topic) return $this->isAjax()
            ? $this->respondJson(['status' => 'error', 'message' => 'Теми не знайдено', 'redirect' => '/topics']) : $this->redirect("/topics");

        $categories = Categories::findAll();
        $categoriesDataForJs = [];
        foreach ($categories as $category) {
            if (method_exists($category, 'toArray')) {
                $categoriesDataForJs[] = $category->toArray();
            } else {
                $categoriesDataForJs[] = [
                    'id' => $category->fields_array['id'] ?? null,
                    'name' => $category->fields_array['name'] ?? null,
                    'description' => $category->fields_array['description'] ?? null,
                ];
            }
        }
        $this->template->setParam('categories', $categoriesDataForJs);
        $this->template->setParam('topic', $topic);

        $sort = $this->get->sort;

        $sortMap = [
            'date_desc' => ['created_at' => 'DESC'],
            'date_asc' => ['created_at' => 'ASC'],
            'title_asc' => ['title' => 'ASC'],
            'title_desc' => ['title' => 'DESC'],
        ];
        $order = isset($sortMap[$sort]) ? $sortMap[$sort] : ['created_at' => 'DESC'];

        $posts = Posts::findByCondition(['topic_id' => $topic->id], $order);
        if ($this->isAjax()) {
            $this->template->setParam('posts', $posts);
            echo $this->template->renderPartial('posts/_list_posts');
            exit;
        }
        $this->template->Title = "Перегляд теми";
        $this->template->setParam('posts', $posts);
        return $this->render();
    }

    public function actionAdd()
    {
        if (!Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax() ? $this->respondJson(['status' => 'error', 'message' => 'Увійдіть у систему', 'redirect' => '/users/login']) : $this->redirect("/users/login");
        }
        $this->clearErrorMessage();

        if ($this->isPost) {

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $this->post = (object)$data;

            $userId = Users::getCurrentUser()->id;
            $categoryId = $this->post->category_id ?? null;

            $this->Validate();

            if (!$this->isErrorMessageExists()) {
                Topics::createTopic($userId, $data['title'], $data['content'], $categoryId);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'redirect' => '/topics?category=' . $categoryId]) : $this->redirect('/topics?category=' . $categoryId);
            } else {
                $this->template->setParam('title', $data['title']);
                $this->template->setParam('content', $data['content']);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => implode('<br>', $this->getErrorMessages()),]) :
                    $this->render();
            }
        }

        $this->template->Title = "Додавання теми";

        $categories = Categories::findAll();
        $this->template->setParam('categories', $categories);

        return $this->render();
    }

    public function actionDelete($params)
    {
        if (!Users::IsUserLogged()){
            http_response_code(403);
            return $this->redirect('/users/login');
        }
        
        $id = $params[0] ?? null;
        if (!$id)
            return $this->redirect('/topics');

        $topic = Topics::FindById($id);
        if (!$topic)
            return $this->redirect('/topics');
        if ($topic->user_id !== Users::getCurrentUser()->id) {
            http_response_code(403);
            return $this->redirect('/topics');
        }

        Topics::deleteById($id);
        return $this->redirect('/topics');
    }

    public function actionEdit($params)
    {

        if (!Users::IsUserLogged()){
            http_response_code(403);
            return $this->isAjax() ? $this->respondJson(['status' => 'error', 'message' => 'Увійдіть у систему', 'redirect' => '/users/login']) : $this->redirect('/users/login');
        }
        
        $id = $params[0] ?? null;

        if (!$id)
            return $this->isAjax() ? $this->respondJson(['status' => 'error', 'message' => 'Теми з таким id не існує', 'redirect' => '/topics']) : $this->redirect("/topics");

        $topic = Topics::FindById($id);
        if (empty($topic)) {
            return $this->isAjax() ? $this->respondJson(['status' => 'error', 'message' => 'Теми не знайдено', 'redirect' => '/topics']) : $this->redirect("/topics");
        }
        if ($topic->user_id !== Users::getCurrentUser()->id) {
            http_response_code(403);
            return $this->isAjax() ? $this->respondJson(['status' => 'error', 'message' => 'Ви не автор цієї теми', 'redirect' => '/topics']) : $this->redirect("/topics");
        }

        $this->clearErrorMessage();

        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $this->post = (object)$data;

            $this->Validate();

            $categoryId = $this->post->category_id ?? null;

            if (!$this->isErrorMessageExists()) {
                Topics::editTopic(
                    $topic->id,
                    $data["title"],
                    $data['content'],
                    $categoryId
                );
                $updatedTopic = Topics::FindById($id);
                return $this->isAjax() ? $this->respondJson([
                    'status' => 'success',
                    'topic' => [
                        'id' => $updatedTopic->id,
                        'title' => $updatedTopic->title,
                        'content' => $updatedTopic->content,
                        'category_id' => $updatedTopic->category_id,
                    ]
                ]) : $this->redirect('/users/view/' . $topic->id);
            } else {
                return $this->isAjax() ? $this->respondJson([
                    'status' => 'error',
                    'message' => implode('<br>', $this->getErrorMessages()),
                ]) : $this->redirect('/users/view/' . $topic->id);;
            }
        } else {
            $this->template->setParam('title', $topic->title);
            $this->template->setParam('content', $topic->content);
        }
        $this->redirect("/topics/view/{$topic->id}");

        $this->render();
    }

    protected function Validate()
    {
        $title = trim($this->post->title ?? '');
        $content = trim($this->post->content ?? '');
        $categoryId = $this->post->category_id ?? null;
        if (empty($title)) {
            $this->addErrorMessage('Заголовок теми не може бути порожнім.');
        } else if (mb_strlen($title) > 50) {
            $this->addErrorMessage('Заголовок теми занадто довгий (макс. 255 символів).');
        }
        if (empty($content)) {
            $this->addErrorMessage('Зміст теми не може бути порожнім.');
        } else if (mb_strlen($content) < 10) {
            $this->addErrorMessage('Зміст теми занадто короткий (мін. 10 символів).');
        }
        if (empty($categoryId)) {
            $this->addErrorMessage('Потрібно обрати категорію.');
        }
    }
}
