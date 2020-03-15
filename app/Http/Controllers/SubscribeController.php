<?php

namespace App\Http\Controllers;

use App\Repositories\SubsribeRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{

    private $subscribeRepository;

    public function __construct(SubsribeRepository $subscribeRepository)
    {
        $this->subscribeRepository = $subscribeRepository;
    }

    public function subscribe($id)
    {
        $currentUser = Auth::user();
        $user = User::FindOrFail($id);

        if($currentUser->id !== $user->id) {
            $this->subscribeRepository->subscribe($currentUser->id, $user->id);
        }

        return redirect()->route('user.index');
    }

    public function unsubscribe($id)
    {
        $currentUser = Auth::user();
        $user = User::FindOrFail($id);

        if($currentUser->id !== $user->id) {
            $this->subscribeRepository->unsubscribe($currentUser->id, $user->id);
        }

        return redirect()->route('user.index');
    }
}
