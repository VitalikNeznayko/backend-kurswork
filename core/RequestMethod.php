<?php

namespace core;

class RequestMethod
{
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }
    public function __get($name)
    {
        if (!isset($this->array[$name])) {
            return null;
        }
        return $this->array[$name];
    }
    public function getAll()
    {
        return $_POST;
    }
}
