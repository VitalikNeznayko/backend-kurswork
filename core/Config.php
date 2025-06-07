<?php
namespace core;

class Config
{
    protected $params;
    protected static $instance = null;
    private function __construct() {
        $directory = "config";
        $configFiles = scandir($directory);

        foreach ($configFiles as $file) {
            if (substr($file, -4) === ".php"){
                $path = $directory . "/" . $file;
                include($path);
            }
        };
        $this->params = [];
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
    }
    public static function get()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }
    public function __get($name)
    {
        return $this->params[$name];
    }
}