<?php

namespace core;

class Session
{
    public function set($key, $value)
    {


        $_SESSION[$key] = $value;
    }
    public function setValues($assocArray)
    {
        foreach ($assocArray as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
    public function get($key)
    {
        if (empty($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }
    public function remove($key)
    {
        unset($_SESSION[$key]);
    } 
}
