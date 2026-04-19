<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public float $latitude,
        public float $longitude
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->id),
            new PrivateChannel('customer.' . $this->order->customer_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timestamp' => now(),
        ];
    }
}
