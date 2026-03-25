<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KitchenOrderBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $businessId;
    public $locationId;
    public $stationId;
    public $updateType;
    public $orderData;

    /**
     * Create a new event instance.
     */
    public function __construct($businessId, $locationId = null, $stationId = null, $updateType = 'order_updated', $orderData = [])
    {
        $this->businessId = $businessId;
        $this->locationId = $locationId;
        $this->stationId = $stationId;
        $this->updateType = $updateType;
        $this->orderData = $orderData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [];
        
        // Main business channel
        $channels[] = new Channel('kitchen.' . $this->businessId);
        
        // Location-specific channel if provided
        if ($this->locationId) {
            $channels[] = new Channel('kitchen.' . $this->businessId . '.location.' . $this->locationId);
        }
        
        // Station-specific channel if provided
        if ($this->stationId) {
            $channels[] = new Channel('kitchen.' . $this->businessId . '.station.' . $this->stationId);
        }
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'kitchen.update';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'type' => $this->updateType,
            'business_id' => $this->businessId,
            'location_id' => $this->locationId,
            'station_id' => $this->stationId,
            'data' => $this->orderData,
            'timestamp' => now()->toISOString()
        ];
    }
}