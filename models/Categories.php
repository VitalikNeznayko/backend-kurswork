<?php

namespace models;

use core\Model;
use DateTime;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 */
class Categories extends Model
{
    public static $tableName= 'categories';

    public static function getAllCategories(): array|null
    {
        return self::findByCondition([]);
    }
    public static function createCategory($name, $description)
    {
        $category = new self();
        $category->name = $name;
        $category->description = $description;
        $category->save();
        return $category;
    }

    public static function editCategory($id, $name, $description)
    {
        $category = self::FindById($id);
        if ($category) {
            $category->name = $name;
            $category->content = $description;
            $category->save();
        }
    }
}
