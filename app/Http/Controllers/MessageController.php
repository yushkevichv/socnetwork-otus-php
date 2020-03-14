<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    private $messageRepository;


    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $userId = Auth::id();
        // @todo add check for auth and gates
        $chat = $this->messageRepository->getChatById($id);
        $chatUser = $this->messageRepository->getChatUsers($chat->id, $userId)->first();
        $messages = $this->messageRepository->getMessagesForChat($id, $userId);

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
        $this->messageRepository->store($data);

        return redirect()->route('messages.user_index', ['id' => $chat->id]);
    }

}
