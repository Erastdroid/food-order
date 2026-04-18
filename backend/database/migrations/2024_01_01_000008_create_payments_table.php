<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('payment_method', [
                'card',
                'cash',
                'wallet',
                'apple_pay',
                'google_pay'
            ]);
            $table->decimal('amount', 10, 2);
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'refunded'
            ])->default('pending');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('payment_gateway')->nullable(); // stripe, paypal, etc.
            $table->json('payment_details')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
