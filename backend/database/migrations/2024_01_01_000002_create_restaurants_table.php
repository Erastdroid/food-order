<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('phone');
            $table->string('email')->unique();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('category'); // italian, chinese, fast_food, etc.
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('total_reviews')->default(0);
            $table->decimal('average_delivery_time', 5, 2)->default(30);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('minimum_order', 8, 2)->default(0);
            $table->boolean('is_open')->default(true);
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('total_orders')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
