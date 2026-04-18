@component('mail::message')
# ❌ Your Order Has Been Cancelled

Hi {{ $order->customer->name }},

Your order has been cancelled.

## Order Details

**Order Number:** #{{ $order->order_number }}
**Restaurant:** {{ $order->restaurant->name }}
**Original Total:** ${{ number_format($order->total_amount, 2) }}

**Cancellation Reason:**
{{ $reason }}

## Refund Status

✅ Your payment of **${{ number_format($order->total_amount, 2) }}** has been refunded to your original payment method.

*Refunds typically appear within 3-5 business days.*

## What Went Wrong?

We'd appreciate your feedback to help us improve. If you'd like to tell us what happened:

@component('mail::button', ['url' => config('app.frontend_url') . '/support'])
Contact Support
@endcomponent

Thank you for your understanding. We look forward to serving you better next time!

@endcomponent
