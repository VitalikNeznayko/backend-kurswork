<?php

namespace core;

class Router
{
    protected $route;

    public function __construct($route)
    {
        $this->route = $route;
    }

    public function run(): mixed
    {
        $parts = explode('/', $this->route);
        if (strlen($this->route) == 0) {
            $parts[0] = "site";
            $parts[1] = "index";
        }

        if (count($parts) == 1) {
            $parts[1] = "index";
        }
        Core::get()->moduleName = $parts[0];
        Core::get()->actionName = $parts[1];

        $controllerName = 'controllers\\' . ucfirst($parts[0]) . 'Controller';
        $method = "action" . ucfirst($parts[1]);

        if (class_exists($controllerName)) {
            $controllerObject = new $controllerName();
            Core::get()->controllerObject = $controllerObject;
            if (method_exists($controllerObject, $method)) {
                array_splice($parts, 0, 2);
                $result = $controllerObject->$method($parts);
                return $result;
            } else {
                $parts[0] = "error";
                $parts[1] = "404";
                Core::get()->moduleName = $parts[0];
                Core::get()->actionName = $parts[1];
                $controllerName = 'controllers\\' . ucfirst($parts[0]) . 'Controller';
                $method = "action" . ucfirst($parts[1]);
                if (class_exists($controllerName) && method_exists(new $controllerName(), $method)) {
                    $controllerObject = new $controllerName();
                    Core::get()->controllerObject = $controllerObject;
                    return $controllerObject->$method([]);
                }
            }
        } else {
            $parts[0] = "error";
            $parts[1] = "404";
            Core::get()->moduleName = $parts[0];
            Core::get()->actionName = $parts[1];
            $controllerName = 'controllers\\' . ucfirst($parts[0]) . 'Controller';
            $method = "action" . ucfirst($parts[1]);
            if (class_exists($controllerName) && method_exists(new $controllerName(), $method)) {
                $controllerObject = new $controllerName();
                Core::get()->controllerObject = $controllerObject;
                return $controllerObject->$method([]);
            }
        }
        return [];
    }
}
