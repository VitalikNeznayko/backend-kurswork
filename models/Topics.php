<?php

namespace models;

use core\Model;
use DateTime;
/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $title
 * @property string $content
 * @property string $created_at
 */
class Topics extends Model
{
    public static $tableName= 'topics';
    public static function createTopic($userId, $title, $content, $categoryId = 0)
    {
        $topic = new self();
        $topic->category_id = $categoryId;
        $topic->user_id = $userId;
        $topic->title = $title;
        $topic->content = $content;
        $topic->created_at = (new DateTime())->format('Y-m-d H:i:s');
        $topic->save();
        return $topic;
    }

    public static function editTopic($id, $title, $content, $categoryId = null)
    {
        $topic = self::FindById($id);
        if ($topic) {
            $topic->title = $title;
            $topic->content = $content;
            $topic->category_id = $categoryId;
            $topic->save();
        }
    }
}
