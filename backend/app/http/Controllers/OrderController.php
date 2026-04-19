<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

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
            ]);

            $totalAmount += $itemTotal;
        }

        $order->update(['total_amount' => $totalAmount]);

        return response()->json($order->load('items'), 201);
    }

    public function index()
    {
        $orders = Order::where('customer_id', auth()->id())
            ->with('items', 'restaurant')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);

        return response()->json($order->load('items', 'restaurant', 'deliveryPerson'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $this->authorize('update', $order);

        $order->update(['status' => $request->input('status')]);

        // Broadcast event for real-time updates
        broadcast(new \App\Events\OrderStatusChanged($order));

        return response()->json($order);
    }
}
