<?php

namespace App\Jobs;

use App\Models\Post;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class UpdateFeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, Post $post)
    {
        $this->userId = $userId;
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::findOrFail($this->post->author_id);

        $data = [
          'content' => $this->post->content,
          'created_at' => $this->post->created_at->toDateTimeString(),
          'author_name' => $user->name,
          'author_last_name' => $user->last_name,
        ];

        Redis::lpush('user:feed:'.$this->userId, json_encode($data));
    }
}
