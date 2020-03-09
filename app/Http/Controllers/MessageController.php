<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = Auth::user();

        // @todo add check for auth and gates
        $chat = Chat::find($id);
        $chatUsers = $chat->users->except($user->id);
        // get just one user; for group chats need refactor
        $chatUser = $chatUsers->first();

        $messages = Message::query()->where('chat_id', $chat->id)->where('user_id', $user->id)->with('author')->orderByDesc('created_at')->get();

        return view('users.messages', compact('chat', 'messages', 'chatUser'  ));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // preparing data
        $from = Auth::id();
        $chat = Chat::FindOrFail($request->id);
        $now = now();
        $commonData = [
            'chat_id' => $chat->id,
            'text' => $request->message,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $data = [];

        foreach ($chat->users as $user) {
            $data[] = array_merge($commonData, [
                'user_id' => $user->id,
                'author_id' => $from
            ]);
        }

        // insert data, maybe should use queue or transaction
        Message::insert($data);

        return redirect()->route('messages.user_index', ['id' => $chat->id]);
    }

}
