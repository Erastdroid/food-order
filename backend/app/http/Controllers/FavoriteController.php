<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'type' => 'required|in:restaurant,menu_item',
        ]);

        $existing = Favorite::where('user_id', auth()->id())
            ->where('type', $validated['type'])
            ->when($validated['restaurant_id'], function ($query) use ($validated) {
                $query->where('restaurant_id', $validated['restaurant_id']);
            })
            ->when($validated['menu_item_id'], function ($query) use ($validated) {
                $query->where('menu_item_id', $validated['menu_item_id']);
            })
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorite = false;
        } else {
            Favorite::create([
                'user_id' => auth()->id(),
                'restaurant_id' => $validated['restaurant_id'] ?? null,
                'menu_item_id' => $validated['menu_item_id'] ?? null,
                'type' => $validated['type'],
            ]);
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'isFavorite' => $isFavorite,
        ]);
    }

    public function getFavorites()
    {
        $restaurants = Favorite::where('user_id', auth()->id())
            ->where('type', 'restaurant')
            ->with('restaurant')
            ->get()
            ->pluck('restaurant');

        $menuItems = Favorite::where('user_id', auth()->id())
            ->where('type', 'menu_item')
            ->with('menuItem')
            ->get()
            ->pluck('menuItem');

        return response()->json([
            'restaurants' => $restaurants,
            'menuItems' => $menuItems,
        ]);
    }
}
