<?php

namespace App\Http\Controllers;

use App\Repositories\FeedRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $posts = $this->feedRepository->getWall(Auth::id());
        return view('users.wall', compact('posts' ));
    }
}
