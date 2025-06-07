<?php

namespace core;

class Core{
    public $defaultLayoutPath = "Views/layouts/index.php";
    public $moduleName;
    public $actionName;
    public $router;
    public $db;
    public $template;
    public $controllerObject;
    private static $instance;
    public $session;
    private $components;
    private function __construct(){
        session_start();
        $this->components = [];
        $this->template = new Template($this->defaultLayoutPath);
        $host = Config::get()->dbHost;
        $name = Config::get()->dbName;
        $login = Config::get()->dbLogin;
        $password = Config::get()->dbPassword;
        $this->db = new DB($host, $name, $login, $password);
        $this->session = new Session();
    }

    public function run($route){
        $this->router = new Router($route);
        
        $params = $this->router->run();
        if(!empty($params)){
            $this->template->setParams($params);
        }
    }

    public function done() {
        $this->template->display();
    }

    public static function get(){
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function setComponent($name, $component)
    {
        $this->components[$name] = $component;
    }

    public function getComponent($name)
    {
        return $this->components[$name] ?? null;
    }
}