<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Events\DeliveryLocationUpdated;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function trackOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        $this->authorize('view', $order);

        return response()->json([
            'order_id' => $order->id,
            'status' => $order->status,
            'delivery_person_location' => $order->deliveryPerson ? [
                'latitude' => $order->deliveryPerson->latitude,
                'longitude' => $order->deliveryPerson->longitude,
            ] : null,
            'delivery_address' => [
                'latitude' => $order->delivery_latitude,
                'longitude' => $order->delivery_longitude,
                'address' => $order->delivery_address,
            ],
            'restaurant_address' => [
                'latitude' => $order->restaurant->latitude,
                'longitude' => $order->restaurant->longitude,
                'address' => $order->restaurant->address,
            ],
            'estimated_delivery_time' => $order->estimated_delivery_time,
        ]);
    }

    public function updateDeliveryLocation(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if (auth()->id() !== $order->delivery_person_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        broadcast(new DeliveryLocationUpdated(
            $order,
            $validated['latitude'],
            $validated['longitude']
        ));

        return response()->json(['success' => true]);
    }
}
