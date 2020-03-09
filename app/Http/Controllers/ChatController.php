<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = Auth::user()->chats;

//        return view('chats.index', compact('chats',   ));
    }

    public function store(Request $request)
    {
        $to = $request->to ?? User::query()->limit(10)->get()->shuffle()->first()->id;
        $from = Auth::id();

        $chat = DB::selectOne(
            DB::raw(
            'select t1.chat_id as id from 
                        (select * from chats_users where user_id = ?) as t1 
                        join
                        (select * from chats_users where user_id = ?) as t2
                        on (t1.chat_id = t2.chat_id)
                    '),
            [
                $from,
                $to
            ]);


        if($chat) {
            // @todo add check for non exist chat
            return redirect()->route('messages.user_index', ['id' => $chat->id]);
        }

        $chat = Chat::create([]);

        $chat->users()->attach([$from, $to]);


        return redirect()->route('messages.user_index', ['id' => $chat->id]);
    }

    public function messages(Request $request, $id)
    {
        $user = Auth::user();

        // @todo add check for auth and gates
        $chat = Chat::find($id);
//        dd($chat);
        $chatUsers = $chat->users->except($user->id);
        // get just one user; for group chats need refactor
        $chatUser = $chatUsers->first();

        $messages = Message::query()->where('chat_id', $chat->id)->get();

        return view('users.messages', compact('chat', 'messages', 'chatUser'  ));
    }
}
