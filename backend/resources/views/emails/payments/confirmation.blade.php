@component('mail::message')
# 💳 Payment Confirmation

Hi {{ $payment->user->name }},

Your payment has been successfully processed!

## Payment Details

**Transaction ID:** {{ $payment->transaction_id }}
**Payment Method:** {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
**Amount:** ${{ number_format($payment->amount, 2) }}
**Status:** ✅ Paid

**Order Number:** #{{ $order->order_number }}
**Restaurant:** {{ $order->restaurant->name }}

**Date & Time:** {{ $payment->paid_at->format('M d, Y h:i A') }}

## What's Next?

Your order has been confirmed and is being prepared. You'll receive updates as your order progresses.

@component('mail::button', ['url' => config('app.frontend_url') . '/orders/' . $order->id . '/track'])
Track Your Order
@endcomponent

## Invoice

**Subtotal:** ${{ number_format($order->subtotal, 2) }}
**Delivery Fee:** ${{ number_format($order->delivery_fee, 2) }}
**Tax:** ${{ number_format($order->tax, 2) }}
@if($order->discount > 0)
**Discount:** -${{ number_format($order->discount, 2) }}
@endif
**Total:** ${{ number_format($order->total_amount, 2) }}

Thank you for your payment!

@endcomponent
