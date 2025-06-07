<?php

namespace core;

class Template
{
    protected $templateFilePath;
    protected $params;
    public $controller;
    protected string $viewsPath = __DIR__ . '/../views/';
    public function __set($name, $value)
    {
        Core::get()->template->setParam($name, $value);
    }
    public function __construct($templateFilePath)
    {
        $this->templateFilePath = $templateFilePath;
        $this->params = [];
    }

    public function setTemplateFilePath($filePath)
    {
        $this->templateFilePath = $filePath;
    }
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }
    public function getParam($name)
    {
        return $this->params[$name];
    }
    public function setParams(array $params)
    {
        foreach ($params as $name => $value) {
            $this->setParam($name, $value);
        }
    }
    public function getHTML(): string
    {
        if (!file_exists($this->templateFilePath)) {
            throw new \Exception("Template file not found: " . $this->templateFilePath);
        }
        
        $this->controller = Core::get()->controllerObject; 
        extract($this->params); 
        ob_start();
        include $this->templateFilePath; 
        return ob_get_clean();
    }
    public function display()
    {
        echo $this->getHTML();
    }
    public function renderPartial(string $template): string
    {
        $file = $this->viewsPath . $template . '.php'; 
        if (!file_exists($file)) {
            throw new \Exception("Template {$template} not found at {$file}");
        }
        extract($this->params);
        ob_start(); 
        include $file; 
        return ob_get_clean(); 
    }
    public function getPageHTML(string $contentTemplatePath): string
    {
        $oldParams = $this->params;

        $content = $this->renderPartial($contentTemplatePath);
        $this->params['Content'] = $content; 

        $fullHtml = $this->getHTML(); 

        $this->params = $oldParams; 
        return $fullHtml;
    }
}
