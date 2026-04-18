<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_pay_with_wallet()
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 100,
        ]);
        $order = Order::factory()->create([
            'customer_id' => $user->id,
            'total_amount' => 50,
        ]);

        $response = $this->actingAs($user)->postJson('/api/payments/wallet', [
            'order_id' => $order->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'order']);

        $this->assertEquals(50, $wallet->fresh()->balance);
    }
}
