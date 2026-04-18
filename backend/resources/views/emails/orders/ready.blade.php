@component('mail::message')
# 📦 Your Order is Ready!

Hi {{ $order->customer->name }},

Exciting news! Your order from **{{ $restaurant->name }}** is ready and waiting for pickup!

## Order Details

**Order Number:** #{{ $order->order_number }}
**Restaurant:** {{ $restaurant->name }}
**Status:** Ready for Delivery 📦

**Pickup Address:**
{{ $restaurant->address }}

**Total Amount:** ${{ number_format($order->total_amount, 2) }}

## What's Next?

🚗 A delivery partner is being assigned and will pick up your order shortly. You'll receive a notification with their details and real-time tracking.

@component('mail::button', ['url' => config('app.frontend_url') . '/orders/' . $order->id . '/track'])
Track Your Order
@endcomponent

Thank you for choosing Talabat! 🎉

@endcomponent
