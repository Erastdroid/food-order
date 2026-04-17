<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'status',
        'payment_status',
        'customer_id',
        'restaurant_id',
        'delivery_person_id'
    ];

    protected $attributes = [
        'status' => 'pending', // default status
        'payment_status' => 'pending' // default payment status
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }

    public function deliveryPerson() {
        return $this->belongsTo(DeliveryPerson::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}