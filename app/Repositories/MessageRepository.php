<?php


namespace App\Repositories;


use App\Models\Chat;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{

    public function getChatById($id) : ?Chat
    {
        return  Chat::find($id);
    }

    public function getChatUsers($chatId, $currentUserId) :Collection
    {
        $chat = $this->getChatById($chatId);
        $chatUsers = $chat->users->except($currentUserId);
        // get just one user; for group chats need refactor

        return $chatUsers;
    }

    public function getMessagesForChat($chatId, $userId)
    {
        $messages = Message::query()
            ->where('chat_id', $chatId)
            ->where('user_id', $userId)
            ->with('author')
            ->orderByDesc('created_at')
            ->get();

        return $messages;
    }

    public function store($preparedData)
    {
        dd(collect($preparedData)->keyBy('user_id'));

        foreach ($preparedData as $key => $value) {

        }

        $data = [];
        // @todo refactor to use shards
        Message::insert($data);
    }

}