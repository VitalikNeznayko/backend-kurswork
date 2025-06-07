<?php

namespace controllers;

use core\Controller;
use core\Core;
use models\Users;

class UsersController extends Controller
{

    public function actionLogin()
    {
        if (Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Ви вже авторизовані', 'redirect' => '/'])
                : $this->redirect('/');
        }

        $message = Core::get()->session->get("success_message");
        if ($message) {
            $this->template->setParam("success_message", $message);
            Core::get()->session->remove("success_message");
        }

        if ($this->isPost) {
            $res = Users::FindByLoginAndPassword($this->post->login, $this->post->password);
            if (!empty($res)) {
                Core::get()->session->set("user", $res);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'redirect' => '/users/profile'])
                    : $this->redirect('/users/profile');
            } else {
                $this->addErrorMessage("Невірний email або пароль");
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => 'Невірний email або пароль'])
                    : $this->render();
            }
        }
        $this->template->Title = "Вхід в аккаунт";
        return $this->render();
    }


    public function actionRegister()
    {
        if (Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Ви вже авторизовані', 'redirect' => '/'])
                : $this->redirect('/');
        }

        $this->clearErrorMessage();
        if ($this->isPost) {
            $existingUser = Users::FindByLogin($this->post->login);
            if (!empty($existingUser)) {
                $this->addErrorMessage("Користувач з таким email вже існує");
            }

            $this->isFirstNameValid();
            $this->isLastNameValid();
            $this->isLoginValid();
            $this->isPasswordValid();
            $this->isRepeatPasswordValid();

            if (!$this->isErrorMessageExists()) {
                $hashedPassword = password_hash($this->post->password, PASSWORD_DEFAULT);
                Users::RegisterUser(
                    $this->post->login,
                    $hashedPassword,
                    $this->post->lastname,
                    $this->post->firstname
                );
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'redirect' => '/users/success'])
                    : $this->redirect('/users/success');
            } else {
                $errors = $this->getErrorMessages();
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => implode('<br> ', $errors)])
                    : $this->render();
            }
        }
        $this->template->Title = "Реєстрація";
        return $this->render();
    }


    public function actionProfile()
    {
        if (!Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Увійдіть у систему', 'redirect' => '/users/login'])
                : $this->redirect('/users/login');
        }

        $user = Users::getCurrentUser();
        $this->template->setParam('user', $user);

        if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['photo']['tmp_name'];
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $userId = $user->id;
            $allowedExt = ['jpg', 'jpeg', 'png'];

            if (!in_array(strtolower($ext), $allowedExt)) {
                return $this->respondJson(['status' => 'error', 'message' => 'Непідтримуваний формат файлу']);
            }

            $uploadDir = __DIR__ . '/../public/images/users/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($allowedExt as $oldExt) {
                $oldPath = $uploadDir . $userId . '.' . $oldExt;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $filePath = $uploadDir . $userId . '.' . $ext;
            if (move_uploaded_file($fileTmp, $filePath)) {
                $avatarPath = Users::GetAvatarPath($user);
                $this->template->setParam('avatarPath', $avatarPath);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'avatarPath' => $avatarPath])
                    : $this->redirect('/users/profile');
            } else {
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => 'Помилка завантаження файлу'])
                    : $this->redirect('/users/profile');
            }
        }
        $this->template->Title = "Особистий профіль";
        $avatarPath = Users::GetAvatarPath($user);
        $this->template->setParam('avatarPath', $avatarPath);
        return $this->render();
    }

    public function actionEdit()
    {
        if (!Users::IsUserLogged()) {
            http_response_code(403);
            return $this->isAjax()
                ? $this->respondJson(['status' => 'error', 'message' => 'Увійдіть у систему', 'redirect' => '/users/login'])
                : $this->redirect('/users/login');
        }

        $this->clearErrorMessage();
        $user = Users::getCurrentUser();
        $this->template->setParam("user", $user);

        if ($this->isPost) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            $this->post = (object)$data;
            $newPassword = $data['password'] ?? '';
            $hashedPassword = $user->password;
            $hasErrors = false;

            $this->isFirstNameValid();
            $this->isLastNameValid();
            $this->isLoginValid();

            if (!empty($newPassword)) {
                if (!$this->isPasswordValid()) {
                    $hasErrors = true;
                }
                if (!$this->isRepeatPasswordValid()) {
                    $hasErrors = true;
                }
                if (!$hasErrors) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                }
            }

            if (!$this->isErrorMessageExists()) {
                Users::EditUser(
                    $user->id,
                    $data['login'],
                    $hashedPassword,
                    $data['lastname'],
                    $data['firstname']
                );
                $updatedUser = Users::FindByLogin($data['login']);
                Users::LoginUser($updatedUser);
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'success', 'user' => [
                        'id' => $updatedUser->id,
                        'login' => $updatedUser->login,
                        'firstname' => $updatedUser->firstName,
                        'lastname' => $updatedUser->lastName,
                    ]])
                    : $this->redirect('/users/profile');
            } else {
                $errors = $this->getErrorMessages();
                return $this->isAjax()
                    ? $this->respondJson(['status' => 'error', 'message' => implode('<br> ', $errors)])
                    : $this->render();
            }
        }
        $this->redirect("/users/profile");
    }

    public function actionSuccess()
    {
        Core::get()->session->set("success_message", "Ви успішно зареєстровані. Тепер ви можете увійти на сайт.");
        return $this->redirect('/users/login');
    }

    public function actionLogout()
    {
        Users::LogoutUser();
        return $this->redirect("/users/profile");
    }
    protected function isFirstNameValid(): bool
    {
        $firstname = $this->post->firstname ?? '';
        if (empty($firstname)) {
            $this->addErrorMessage("Ім’я не може бути порожнім");
            return false;
        } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-ZґҐіІїЇєЄ\'\- ]+$/u', $firstname)) {
            $this->addErrorMessage("Ім’я повинно містити лише літери");
            return false;
        }
        return true;
    }
    protected function isLastNameValid(): bool
    {
        $lastname = $this->post->lastname ?? '';
        if (empty($lastname)) {
            $this->addErrorMessage("Прізвище не може бути порожнім");
            return false;
        } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-ZґҐіІїЇєЄ\'\- ]+$/u', $lastname)) {
            $this->addErrorMessage("Прізвище повинно містити лише літери");
            return false;
        }
        return true;
    }
    protected function isRepeatPasswordValid(): bool
    {
        $password = $this->post->password ?? '';
        $repeatPassword = $this->post->repeatPassword ?? '';
        if ($password !== $repeatPassword) {
            $this->addErrorMessage("Паролі не збігаються");
            return false;
        }
        return true;
    }
    public function isLoginValid(): bool
    {
        $login = $this->post->login ?? '';
        if (empty($login)) {
            $this->addErrorMessage("Email не може бути порожнім");
            return false;
        } elseif (strlen($login) < 4) {
            $this->addErrorMessage("Email повинен містити щонайменше 4 символи");
            return false;
        } elseif (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $this->addErrorMessage("Email має невірний формат");
            return false;
        }
        return true;
    }
    protected function isPasswordValid(): bool
    {
        $password = $this->post->password ?? '';
        if (empty($password)) {
            $this->addErrorMessage("Пароль не може бути порожнім");
            return false;
        } elseif (strlen($password) < 6) {
            $this->addErrorMessage("Пароль повинен містити щонайменше 6 символів");
            return false;
        } elseif (
            !preg_match('/[A-Z]/',  $password) ||
            !preg_match('/[a-z]/',  $password) ||
            !preg_match('/[0-9]/',  $password)
        ) {
            $this->addErrorMessage("Пароль має містити великі та малі літери і хоча б одну цифру");
            return false;
        }
        return true;
    }
}
