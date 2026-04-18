@component('mail::message')
# ✨ Your Order Has Been Delivered!

Hi {{ $order->customer->name }},

Enjoy your meal! Your order from **{{ $order->restaurant->name }}** has been delivered successfully.

## Order Summary

**Order Number:** #{{ $order->order_number }}
**Total Amount:** ${{ number_format($order->total_amount, 2) }}
**Delivered At:** {{ $order->delivered_at->format('M d, Y h:i A') }}

## Share Your Feedback

We'd love to hear about your experience! Please rate the restaurant, food quality, and delivery service.

@component('mail::button', ['url' => config('app.frontend_url') . '/orders/' . $order->id . '/review'])
Rate This Order
@endcomponent

**Thank you for ordering with Talabat!** 🙏

Your feedback helps us serve you better.

@endcomponent
