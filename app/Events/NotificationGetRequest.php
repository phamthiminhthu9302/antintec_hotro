<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\Payment;
use App\Models\Request;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationGetRequest implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   *  @var Service
   */
  public $service;

  /**
   *  @var User
   */
  public $customer;


  public $technician;

  /**
   *  @var Request
   */
  public $request;


  /**
   * Create a new event instance.
   * @param array $technician

   * @return void
   */
  public function __construct($request, $service, $technician, $customer)
  {
    $this->request = $request;
    $this->service = $service;
    $this->technician = $technician;
    $this->customer = $customer;
  }

  public function broadcastOn(): array
  {
    return ['channel-notification-request'];
  }

  public function broadcastAs()
  {
    return 'my-event-notification-request';
  }
}
