<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'review_type' => 'required|in:restaurant,food,delivery',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->customer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $images[] = $path;
            }
        }

        $review = Review::create([
            'order_id' => $validated['order_id'],
            'customer_id' => auth()->id(),
            'restaurant_id' => $validated['restaurant_id'],
            'menu_item_id' => $validated['menu_item_id'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'images' => $images,
            'review_type' => $validated['review_type'],
            'is_verified_purchase' => true,
        ]);

        // Update restaurant rating
        $this->updateRestaurantRating($validated['restaurant_id']);

        // Update menu item rating if applicable
        if ($validated['menu_item_id']) {
            $this->updateMenuItemRating($validated['menu_item_id']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $review,
        ], 201);
    }

    public function getRestaurantReviews($restaurantId)
    {
        $reviews = Review::where('restaurant_id', $restaurantId)
            ->where('review_type', 'restaurant')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    public function getMenuItemReviews($menuItemId)
    {
        $reviews = Review::where('menu_item_id', $menuItemId)
            ->where('review_type', 'food')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    private function updateRestaurantRating($restaurantId)
    {
        $avgRating = Review::where('restaurant_id', $restaurantId)
            ->where('review_type', 'restaurant')
            ->avg('rating');

        $totalReviews = Review::where('restaurant_id', $restaurantId)
            ->where('review_type', 'restaurant')
            ->count();

        \App\Models\Restaurant::find($restaurantId)->update([
            'rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }

    private function updateMenuItemRating($menuItemId)
    {
        $avgRating = Review::where('menu_item_id', $menuItemId)
            ->avg('rating');

        $totalReviews = Review::where('menu_item_id', $menuItemId)
            ->count();

        \App\Models\MenuItem::find($menuItemId)->update([
            'rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }
}
