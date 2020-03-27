<?php

namespace App\Http\Controllers;

use App\Repositories\FeedRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    private $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function index()
    {
        $posts = $this->feedRepository->getAllUserPosts(Auth::id());

        return view('users.posts', compact('posts'  ));

    }

    public function store(Request $request)
    {
        $data = [
            'author_id' => Auth::id(),
            'content' => $request->message
        ];

        $this->feedRepository->store($data);

        return redirect()->route('post.index');
    }

    public function getWall()
    {
        $userId = Auth::id();
        $posts = $this->feedRepository->getWall($userId);
        Redis::set('user:non_readed:'.$userId, 0);
        return view('users.wall', compact('posts' ));
    }
}
