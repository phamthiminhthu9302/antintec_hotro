<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\Request;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     *  @var Notification
     */
    public $notification_c;
    /**
     *  @var Notification
     */
    public $notification_t;
    /**
     *  @var User
     */
    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Notification $notification_c, Notification $notification_t, User $user)
    {
        $this->notification_c = $notification_c;
        $this->notification_t = $notification_t;
        $this->user = $user;
    }

    public function broadcastOn(): array
    {
        return ['channel-request'];
    }

    public function broadcastAs()
    {
        return 'my-event-request';
    }
}