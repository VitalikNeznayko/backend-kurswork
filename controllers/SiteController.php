<?php
namespace controllers;

use core\Controller;
use core\Core;
use models\Categories;

class SiteController extends Controller
{
    public function actionIndex()
    {
        $cache = Core::get()->getComponent('cache');
        $cacheKey = 'site_index_data';
        $data = $cache->get($cacheKey);

        if ($data === null) {
            $categories = Categories::findAll();
            $data = [
                'categories' => $categories,
            ];
            $cache->set($cacheKey, $data, 3600);
        }

        $this->template->setParams($data);

        return $this->render();
    }
}
