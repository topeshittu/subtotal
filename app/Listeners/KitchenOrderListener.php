<?php

namespace App\Listeners;

use App\Events\SellCreatedOrModified;
use App\Events\KitchenOrderUpdated;
use App\Events\KitchenOrderBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KitchenOrderListener
{
    /**
     * Handle the event for kitchen-related order updates with real-time broadcasting
     *
     * @param  SellCreatedOrModified  $event
     * @return void
     */
    public function handle(SellCreatedOrModified $event)
    {
        $transaction = $event->transaction;
        
        if (!$transaction->is_running_order) {
            return;
        }
        
        \Cache::put('kitchen_last_updated_' . $transaction->business_id, now(), 3600);

        event(new KitchenOrderUpdated(
            $transaction->id,
            null,
            'order_updated',
            $transaction->business_id
        ));
        
    }
}