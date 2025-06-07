<?php

namespace models;

use core\Model;
use DateTime;

/**
 * @property int $id 
 * @property int $post_id 
 * @property int $parent_id
 * @property int $user_id
 * @property string $content
 * @property string $created_at 
 */
class Comments extends Model
{
    public static $tableName= 'comments';

    public static function createComment($post_id, $parent_id, $user_id, $content)
    {
        $comment = new self();
        $comment->post_id = $post_id;
        $comment->parent_id = $parent_id;
        $comment->user_id = $user_id;
        $comment->content = $content;
        $comment->created_at = (new DateTime())->format('Y-m-d H:i:s');
        $comment->save();
        return $comment;
    }

    public static function editComment($id, $content): bool
    {
        $comment = self::FindById($id);
        if ($comment) {
            $comment->content = $content;
            $comment->save();
            return true;
        }
        return false;
    }

}
