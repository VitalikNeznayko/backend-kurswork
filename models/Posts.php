<?php

namespace models;

use core\Model;
use DateTime;

/**
 * @property int $id 
 * @property int $topic_id 
 * @property int $user_id 
 * @property string $title 
 * @property string $content 
 * @property string $created_at  
 * 
 */
class Posts extends Model
{
    public static $tableName = 'posts';

    public static function createPost($topicId, $userId, $title, $content)
    {
        $post = new self();
        $post->topic_id = $topicId;
        $post->user_id = $userId;
        $post->title = $title;
        $post->content = $content;
        $post->created_at = (new DateTime())->format('Y-m-d H:i:s');
        $post->save();
        return $post;
    }

    public static function editPost($id, $title, $content)
    {
        $post = self::FindById($id);
        if ($post) {
            $post->title = $title;
            $post->content = $content;
            $post->save();
        }
    }
    public static function formatTimeAgo($date)
    {
        $now = new DateTime();
        $postDate = new DateTime($date);
        $interval = $postDate->diff($now);

        if ($interval->y > 0) {
            return $interval->y . ' р. тому';
        } elseif ($interval->m > 0) {
            return $interval->m . ' міс.тому';
        } elseif ($interval->d > 0) {
            return $interval->d . ' дн. тому';
        } elseif ($interval->h > 0) {
            return $interval->h . ' год. тому';
        } elseif ($interval->i > 0) {
            return $interval->i . ' хв. тому';
        } else {
            return 'щойно';
        }
    }
}
