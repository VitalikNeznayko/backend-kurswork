<?php

namespace controllers;

use core\Controller;
use models\Comments;
use models\Posts;
use models\Users;

class CommentsController extends Controller
{
    public function actionAdd()
    {

        $this->clearErrorMessage();

        if (!Users::IsUserLogged()) {
            http_response_code(403);
            return $this->respondJson([
                'status' => 'error',
                'message' => 'Увійдіть у систему',
                'redirect' => '/users/login'
            ]);
        }

        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $this->post = (object)$data;

            $userId = Users::getCurrentUser()->id;
            $postId = $this->post->post_id ?? null;
            $parentId = $this->post->parent_id ?? null;
            $content = $this->post->content ?? '';

            if (!Posts::FindById($postId)) {
                return $this->respondJson(['status' => 'error', 'message' => 'Пост не знайдено.']);
            }

            $this->ValidateComment();

            if (!$this->isErrorMessageExists()) {
                $newComment = Comments::createComment($postId, $parentId, $userId, $content);
                
                if ($newComment) {
                    $commentAuthor = Users::FindById($newComment->user_id);
                    $authorName = $commentAuthor
                        ? htmlspecialchars($commentAuthor->lastName . ' ' . $commentAuthor->firstName)
                        : 'Гість';
                    return $this->isAjax() ? $this->respondJson([
                        'status' => 'success',
                        'comment' => [
                            'id' => $newComment->id,
                            'content' => $newComment->content,
                            'created_at_formatted' => date('d.m.Y H:i', strtotime($newComment->created_at)),
                            'authorName' => $authorName,
                            'user_id' => $newComment->user_id,
                            'parent_id' => $newComment->parent_id,
                        ],
                        'currentUser' => [
                            'id' => $userId,
                        ],
                    ]) : $this->redirect('posts/view/' . $newComment->post_id);
                } else {
                    return $this->respondJson(['status' => 'error', 'message' => 'Не вдалося створити коментар у базі даних.']);
                }
            } else {
                return $this->respondJson(['status' => 'error', 'message' => implode('<br>', $this->getErrorMessages())]);
            }
        }

        return $this->respondJson(['status' => 'error', 'message' => 'Неправильний метод запиту.']);
    }

    public function actionEdit($params)
    {
        if (!Users::IsUserLogged()) {
            http_response_code(403);
            $this->redirect("/users/login");
        }

        $id = $params[0] ?? null;
        if (!$id) {
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Невірний ідентифікатор коментаря'])
                : $this->redirect('/posts'); 
        }

        $comment = Comments::FindById($id);
        if (!$comment) {
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Коментар не знайдено'])
                : $this->redirect('/posts'); 
        }

        if ($comment->user_id !== Users::getCurrentUser()->id) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Недостатньо прав для редагування'])
                : $this->redirect('/posts/view/' . $comment->post_id);
        }

        $this->clearErrorMessage();

        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $this->post = (object)$data;

            $content = $this->post->content ?? '';

            $this->ValidateComment();

            if (!$this->isErrorMessageExists()) {
                Comments::editComment($id, $content);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'redirect' => '/posts/view/' . $comment->post_id])
                    : $this->redirect($this->redirect('/posts/view/' . $id));
            } else {
                $this->template->setParam('comment', $comment); 
                $this->template->setParam('content', $content); 
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => implode('<br>', $this->getErrorMessages())])
                    : $this->render();
            }
        }

        $this->template->setParam('comment', $comment);
        $this->template->Title = "Редагування коментаря";
        return $this->render();
    }
    public function actionGet()
    {
        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $postId = $data['post_id'] ?? null;

            if (!Posts::FindById($postId)) {
                return $this->respondJson(['status' => 'error', 'message' => 'Пост не знайдено.']);
            }

            $comments = Comments::findByCondition(['post_id' => $postId], ['created_at' => 'DESC']);
            $userId = Users::IsUserLogged() ? Users::getCurrentUser()->id : null;

            $commentsData = [];
            foreach ($comments as $comment) {
                $commentAuthor = Users::FindById($comment->user_id);
                $authorName = $commentAuthor
                    ? htmlspecialchars($commentAuthor->lastName . ' ' . $commentAuthor->firstName)
                    : 'Гість';

                $commentsData[] = [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at_formatted' => date('d.m.Y H:i', strtotime($comment->created_at)),
                    'authorName' => $authorName,
                    'user_id' => $comment->user_id,
                    'parent_id' => $comment->parent_id,
                ];
            }

            return $this->respondJson([
                'status' => 'success',
                'comments' => $commentsData,
                'currentUser' => $userId ? ['id' => $userId] : null,
            ]);
        }

        return $this->respondJson(['status' => 'error', 'message' => 'Неправильний метод запиту.']);
    }
    public function actionDelete($params)
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
                ? $this->respondJson(['status' => 'error', 'message' => 'Невірний ідентифікатор коментаря'])
                : $this->redirect('/topics');
        }

        $comment = Comments::FindById($id);
        if (!$comment) {
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Коментар не знайдено'])
                : $this->redirect('/topics');
        }

        if ($comment->user_id !== Users::getCurrentUser()->id) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Недостатньо прав для видалення'])
                : $this->redirect('/posts/view/' . $comment->post_id);
        }

        Comments::deleteById($id);
        
        return $this->isAjax()
            ? $this->respondJson(['status' => 'success', 'message' => 'Коментар успішно видалено'])
            : $this->redirect('/posts/view/' . $comment->post_id);
    }

    protected function ValidateComment()
    {
        if (empty($this->post->content)) {
            $this->addErrorMessage('Вміст коментаря не може бути порожнім');
        } elseif (strlen($this->post->content) > 1000) { 
            $this->addErrorMessage('Вміст коментаря не може перевищувати 1000 символів');
        }
    }
}
