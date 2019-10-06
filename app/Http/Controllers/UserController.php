<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->getAll();

        return view('users.index', compact('users'));
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
