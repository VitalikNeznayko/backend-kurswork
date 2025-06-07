<?php

namespace controllers;

use core\Controller;
use core\Post;
use models\Posts;
use models\Users;
use models\Comments;
use models\Topics;

class PostsController extends Controller
{
    public function actionView($params)
    {
        $id = $params[0] ?? null;
        if (!$id) {
            return $this->redirect('/topics');
        }

        $post = Posts::FindById($id);
        if (!$post) {
            return $this->redirect('/topics');
        }

        $comments = Comments::findByCondition(['post_id' => $post->id], ['created_at' => 'DESC']);

        $this->template->setParam('post', $post);
        $this->template->setParam('comments', $comments);
        $this->template->Title = "Перегляд поста";
        return $this->render();
    }

    public function actionAdd()
    {
        if (!Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Увійдіть у систему', 'redirect' => '/users/login'])
                : $this->redirect("/users/login");
        }

        $this->clearErrorMessage();

        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $this->post = (object)$data;

            $userId = Users::getCurrentUser()->id;
            $topicId = $this->post->topic_id ?? null;
            $title = $this->post->title ?? '';
            $content = $this->post->content ?? '';

            $this->Validate();
            $this->ValidatePost();

            if (!$this->isErrorMessageExists()) {
                Posts::createPost($topicId, $userId,  $title, $content);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'redirect' => '/topics/view/' . $topicId])
                    : $this->redirect('/topics/view/' . $topicId);
            } else {
                $this->template->setParam('topic_id', $topicId);
                $this->template->setParam('content', $content);
                $this->template->setParam('title', $title);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => implode('<br>', $this->getErrorMessages())])
                    : $this->render();
            }
        }
        $topics = Topics::findAll(['title' => 'ASC']);
        $this->template->setParam('topics', $topics);
        $this->template->Title = "Додавання поста";
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

        $post = Posts::FindById($id);
        if (!$post)
            return $this->redirect('/topics');
        if ($post->user_id !== Users::getCurrentUser()->id) {
            return $this->redirect('/topics/view/'. $post->topic_id);
        }

        Posts::deleteById($id);
        return $this->redirect('/topics/view/' . $post->topic_id);
    }

    public function actionEdit($params)
    {
        if (!Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Увійдіть у систему', 'redirect' => '/users/login'])
                : $this->redirect("/users/login");
        }

        $id = $params[0] ?? null;
        if (!$id) {
            
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Невірний ідентифікатор поста', 'redirect' => '/topics'])
                : $this->redirect('/topics');
        }

        $post = Posts::FindById($id);
        if (!$post) {
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Пост не знайдено', 'redirect' => '/topics'])
                : $this->redirect('/topics');
        }
        $this->clearErrorMessage();

        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $this->post = (object)$data;

            $content = $this->post->content ?? '';
            $title = $this->post->title;

            $this->Validate();

            if (!$this->isErrorMessageExists()) {
                Posts::editPost($id, $title, $content);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'redirect' => '/posts/view/' . $id])
                    : $this->redirect('/posts/view/' . $id);
            } else {
                $this->template->setParam('post', $post);
                $this->template->setParam('content', $content);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => implode('<br>', $this->getErrorMessages())])
                    : $this->render();
            }
        }

        $this->template->setParam('post', $post);
        $this->template->Title = "Редагування поста";
        return $this->render();
    }

    protected function Validate()
    {
        $this->clearErrorMessage();

        if (empty($this->post->title)) {
            $this->addErrorMessage('Вміст заголовку поста не може бути порожнім');
        } elseif (strlen($this->post->title) > 50) {
            $this->addErrorMessage('Вміст заголовку поста не може перевищувати 50 символів');
        }

        if (empty($this->post->content)) {
            $this->addErrorMessage('Вміст поста не може бути порожнім');
        } elseif (strlen($this->post->content) < 10) {
            $this->addErrorMessage('Вміст поста не може бути менше 10 символів');
        }
    }
    protected function ValidatePost()
    {
        if (empty($this->post->topic_id)) {
            $this->addErrorMessage('Оберіть тему');
        }
    }
}
