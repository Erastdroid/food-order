<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->id),
            new PrivateChannel('customer.' . $this->order->customer_id),
            new PrivateChannel('restaurant.' . $this->order->restaurant_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'updated_at' => $this->order->updated_at,
            'message' => $this->getStatusMessage(),
        ];
    }

    private function getStatusMessage(): string
    {
        return match ($this->order->status) {
            'confirmed' => '✅ Your order has been confirmed!',
            'preparing' => '👨‍🍳 Your food is being prepared',
            'ready' => '📦 Your order is ready for pickup',
            'on_the_way' => '🚗 Your order is on the way!',
            'delivered' => '✨ Your order has been delivered',
            'cancelled' => '❌ Your order has been cancelled',
            default => 'Order status updated',
        };
    }
}
