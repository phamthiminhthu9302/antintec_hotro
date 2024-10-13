<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TechnicianLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $technicianId;
    public float $latitude;
    public float $longitude;
    // public $location;
    /**
     * Create a new event instance.
     */
    public function __construct($technicianId,$latitude,$longitude){
        $this->technicianId = $technicianId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('technician-location');
    }

    public function broadcastAs()
    {
        return 'TechnicianLocationUpdated';
    }
}
