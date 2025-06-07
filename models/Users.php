<?php

namespace models;

use core\Model;
use core\Core;

/**
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property bool $isAdmin
 */
class Users extends Model
{
    public static $tableName = 'users';
    public static function FindByLoginAndPassword($login, $password)
    {
        $userList = self::findByCondition(['login' => $login]);
        $user = $userList[0] ?? null;

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    public static function GetAvatarPath($user)
    {
        if(!$user){
            return "/../public/images/users/default.png";
        }
        ob_clean();
        $fullDir = __DIR__ . '/../public/images/users/';
        $avatarDir = '/../public/images/users/';
        $avatarPath = "/../public/images/users/default.png";

        $allowedExt = ['jpg', 'jpeg', 'png'];
        foreach ($allowedExt as $ext) {
            $fullPath = $fullDir . $user->id . '.' . $ext;
            if (file_exists($fullPath)) {
                $avatarPath = "/..". $avatarDir . $user->id . '.' . $ext;
                break;
            }
        }

        return $avatarPath;
    }
    public static function FindByLogin($login)
    {
        $userList = self::findByCondition(['login' => $login]);
        return $userList[0] ?? null;
    }

    public static function IsUserLogged(): bool
    {
        return !empty(Core::get()->session->get("user"));
    }

    public static function RegisterUser($login, $password, $lastname, $firstname, $isAdmin = 0): void
    {
        $user = new self();
        $user->login = $login;
        $user->password = $password;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->isAdmin = $isAdmin;
        $user->save();
    }

    public static function EditUser($id, $login, $password, $lastname, $firstname): void
    {
        $user = self::FindById($id);
        if ($user) {
            $user->login = $login;
            $user->password = $password;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->save();
        }
    }

    public static function LoginUser($user)
    {
        Core::get()->session->set("user", $user);
    }
    public static function getCurrentUser()
    {
        return Core::get()->session->Get("user");
    }
    public static function LogoutUser()
    {
        Core::get()->session->remove("user");
    }
}
