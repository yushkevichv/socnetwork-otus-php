<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepository;
    const PER_PAGE = 50;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $currentMaxCount = self::PER_PAGE;
        $usersCount = $this->userRepository->getAllCount($request->q);
        if($request->page) {
            $page = $request->page;
            $currentMaxCount = $request->page * self::PER_PAGE;
        }
        else {
            $page = 1;
        }

        if($currentMaxCount < $usersCount) {
            $isMoreExist = true;
        }
        else {
            $isMoreExist = false;
        }

        $query = $request->q ?? null;

        $users = $this->userRepository->getAll(self::PER_PAGE, $currentMaxCount, $query);

        return view('users.index', compact('users', 'usersCount', 'isMoreExist', 'page', 'query'));
    }

    public function show($id)
    {
        $user = $this->userRepository->getById($id);
        if(!$user) {
            abort(404);
        }

        return view('users.show', compact('user'));
    }
}
