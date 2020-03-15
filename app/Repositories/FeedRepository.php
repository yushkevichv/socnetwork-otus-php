<?php


namespace App\Repositories;


use App\Models\Post;

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
        Post::create($data);
    }

}