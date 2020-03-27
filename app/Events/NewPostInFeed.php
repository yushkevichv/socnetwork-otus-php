<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostInFeed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $unReadedMessagesCount;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($unReadedMessagesCount, $userId)
    {
        $this->unReadedMessagesCount = $unReadedMessagesCount;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('feed.'.$this->userId);
    }

    public function broadcastAs()
    {
        return 'feed-updated';
    }
}
