<?php


namespace App\Repositories;


use App\Jobs\UpdateFeeds;
use App\Models\Post;
use Illuminate\Support\Facades\Redis;

class FeedRepository
{
    public function getAllUserPosts($userId) {
        return Post::query()
            ->where('author_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function store($data) :void
    {
        $post = Post::create($data);

        UpdateFeeds::dispatch($post);
    }

    public function getWall($userId)
    {
        $posts = Redis::lrange('user:feed:'.$userId, 0, 1000);
        array_walk($posts, function (&$value) {
            $value = json_decode($value);
            return $value;
        });

        return $posts;
    }

}