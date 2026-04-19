<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('is_open', true)
            ->with('menuItems')
            ->paginate(10);

        return response()->json($restaurants);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('menuItems')->findOrFail($id);
        return response()->json($restaurant);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'delivery_time' => 'required|integer',
            'delivery_fee' => 'required|numeric',
        ]);

        $restaurant = Restaurant::create([
            'owner_id' => auth()->id(),
            ...$validated,
            'is_open' => true,
        ]);

        return response()->json($restaurant, 201);
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        
        $this->authorize('update', $restaurant);

        $restaurant->update($request->validated());

        return response()->json($restaurant);
    }
}
