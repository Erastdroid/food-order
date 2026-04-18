<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $restaurant = Restaurant::factory()->create();
        $menuItem = MenuItem::factory()->create(['restaurant_id' => $restaurant->id]);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'restaurant_id' => $restaurant->id,
            'delivery_address' => '123 Main St',
            'items' => [
                [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'order_number', 'status', 'total_amount']);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
    }

    public function test_user_can_track_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['customer_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/orders/{$order->id}/track");

        $response->assertStatus(200)
            ->assertJsonStructure(['order_id', 'status', 'delivery_address']);
    }
}
