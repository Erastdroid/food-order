<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'delivery_address' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'customer_id' => auth()->id(),
            'restaurant_id' => $validated['restaurant_id'],
            'delivery_address' => $validated['delivery_address'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $menuItem = \App\Models\MenuItem::find($item['menu_item_id']);
            $itemTotal = $menuItem->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price' => $menuItem->price,
                'total' => $itemTotal,
            ]);

            $totalAmount += $itemTotal;
        }

        // Add delivery fee and tax
        $restaurant = $order->restaurant;
        $tax = ($totalAmount * 0.1); // 10% tax
        $deliveryFee = $restaurant->delivery_fee;

        $order->update([
            'subtotal' => $totalAmount,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $totalAmount + $tax + $deliveryFee,
            'estimated_delivery_time' => $restaurant->average_delivery_time,
        ]);

        return response()->json($order->load('items'), 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,on_the_way,delivered,cancelled',
            'cancellation_reason' => 'nullable|string|required_if:status,cancelled',
        ]);

        $order->update([
            'status' => $validated['status'],
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
        ]);

        // Update timestamps based on status
        match ($validated['status']) {
            'confirmed' => $order->update(['confirmed_at' => now()]),
            'ready' => $order->update(['ready_at' => now()]),
            'delivered' => $order->update(['delivered_at' => now()]),
            'cancelled' => $order->update(['cancelled_at' => now()]),
            default => null,
        };

        // Broadcast event (triggers email notifications via listener)
        broadcast(new OrderStatusChanged($order));

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'order' => $order,
        ]);
    }
}
