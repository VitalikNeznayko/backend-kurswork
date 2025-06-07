<?php

namespace controllers;

use core\Controller;

class ErrorController extends Controller
{
    public function action404()
    {
        http_response_code(404);
        return $this->render();
    }
}