<?php


namespace App\Repositories;


use App\Models\Chat;
use App\Models\Message;
use App\Services\ShardMapper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MessageRepository
{
    private $shardMapper;

    public function __construct(ShardMapper $shardMapper)
    {
        $this->shardMapper = $shardMapper;
    }

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
        $data = [];
        $shards = [];

        foreach (collect($preparedData)->keyBy('user_id') as $userId => $messageData) {
            $shard = $this->shardMapper->getShardForMessages($userId);
            $shards[$shard->id] = $shard;
            $data[$shard->id][] = $messageData;
        }

        foreach ($data as $key => $value) {
            $shard = $shards[$key];

            Config::set("database.connections.".$shard->name, [
                'driver' => 'mysql',
                'host' => $shard->host,
                'port' => $shard->port,
                'database' => $shard->db_name,
                'username' => $shard->username,
                'password' => $shard->password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

//            try {
            // check connection
                DB::connection($shard->name)->getDatabaseName();
//            }
//            catch (\Exception $exception) {
//                //
//            }
            Message::on($shard->name)->insert($value);

            unset($shard);
        }
    }

}