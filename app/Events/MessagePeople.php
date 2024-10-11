<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagePeople implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $results;
  public $message;
  public $count;


  /**
   * Tạo event mới.
   *
   * @param array $results
   * @param array $message
   * @param array $count
   * @return void
   */
  public function __construct($results, $message, $count)
  {
    $this->results = $results;
    $this->message = $message;
    $this->count = $count;
  }

  /**
   * Xác định kênh mà sự kiện này sẽ được phát.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    // Định nghĩa kênh bạn muốn phát event lên
    return new Channel('people-message');
  }

  /**
   * Tên event mà sẽ được phát đi.
   *
   * @return string
   */
  public function broadcastAs()
  {
    return 'my-event-people';
  }
}
