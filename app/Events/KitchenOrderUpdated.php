<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is fired when kitchen items or orders change status
 */
class KitchenOrderUpdated
{
    use Dispatchable, SerializesModels;

    public $orderId;
    public $itemId;
    public $newStatus;
    public $businessId;
    public $stationId;
    public $updatedAt;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($orderId, $itemId, $newStatus, $businessId, $stationId = null)
    {
        $this->orderId = $orderId;
        $this->itemId = $itemId;
        $this->newStatus = $newStatus;
        $this->businessId = $businessId;
        $this->stationId = $stationId;
        $this->updatedAt = now();
        
    }
}