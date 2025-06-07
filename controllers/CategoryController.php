<?php

namespace controllers;

use core\Controller;
use core\Core;
use models\Categories;

class CategoryController extends Controller
{
    public function actionIndex()
    {
        $this->template->Title = "Доступні категорії";

        $cache = Core::get()->getComponent('cache');
        $cacheKey = 'category_index_data';

        $data = $cache->get($cacheKey);

        if ($data === null) {
            $categories = Categories::findAll();
            $data = [
                'categories' => $categories,
            ];
            $cache->set($cacheKey, $data, 3600);
        }
        $categories = Categories::findAll();
        $this->template->setParam('categories', $categories);
        return $this->render();
    }
}
