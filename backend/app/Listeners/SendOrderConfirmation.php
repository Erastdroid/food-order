<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderReadyMail;
use App\Mail\OrderOnTheWayMail;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;

        match ($order->status) {
            'confirmed' => Mail::to($order->customer->email)->queue(new OrderConfirmedMail($order)),
            'ready' => Mail::to($order->customer->email)->queue(new OrderReadyMail($order)),
            'on_the_way' => Mail::to($order->customer->email)->queue(
                new OrderOnTheWayMail(
                    $order,
                    $order->deliveryPerson?->name,
                    $order->deliveryPerson?->phone
                )
            ),
            'delivered' => Mail::to($order->customer->email)->queue(new OrderDeliveredMail($order)),
            'cancelled' => Mail::to($order->customer->email)->queue(
                new OrderCancelledMail($order, $order->cancellation_reason ?? 'Unknown')
            ),
            default => null,
        };
    }
}
