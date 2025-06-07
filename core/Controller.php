<?php

namespace core;

class Controller
{
    protected $template;
    protected $errorMessage;
    public $isPost = false;
    public $isGet = false;
    public $post;
    public $get;
    public function __construct()
    {
        $action = Core::get()->actionName;
        $module = Core::get()->moduleName;
        $path = "views/{$module}/{$action}.php";
        $this->post = new Post();
        $this->get = new GET();
        $this->errorMessage = [];
        $this->template = new Template($path);
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->isPost = true;
                break;
            case 'GET':
                $this->isGet = true;
                break;
            default:
                $this->isPost = false;
                $this->isGet = false;
        }
    }

    public function render($pathToView = null): array
    {
        if (!empty($pathToView))
            $this->template->setTemplateFilePath($pathToView);
        return [
            'Content' => $this->template->getHTML(),
        ];
    }
    public function redirect($url): void
    {
        header("Location: {$url}");
        die;
    }

    public function addErrorMessage($message): void
    {
        $this->errorMessage[] = $message;
        $this->template->setParam('error_message', implode("<br/>", $this->errorMessage));
    }
    public function clearErrorMessage(): void
    {
        $this->errorMessage = [];
        $this->template->setParam('error_message', null);
    }
    public function getErrorMessages(): array
    {
        return $this->errorMessage;
    }
    public function isErrorMessageExists(): bool
    {
        return count($this->errorMessage) > 0;
    }
    protected function respondJson($data)
    {
        if (ob_get_length()) {
            ob_clean(); 
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    protected function renderPartial(string $templateName)
    {
        include __DIR__ . "/../views/$templateName.php";
        exit;
    }
    
}
