@component('mail::message')
# 💰 Refund Processed

Hi {{ $payment->user->name }},

Your refund has been successfully processed!

## Refund Details

**Order Number:** #{{ $order->order_number }}
**Refund Amount:** ${{ number_format($payment->refund_amount, 2) }}
**Reason:** {{ $payment->refund_reason }}
**Status:** ✅ Refunded

**Original Transaction ID:** {{ $payment->transaction_id }}
**Refund Date:** {{ $payment->refunded_at->format('M d, Y h:i A') }}

## When Will I See The Refund?

Refunds typically appear in your account within:
- **Credit/Debit Cards:** 3-5 business days
- **Digital Wallets:** 1-2 business days
- **Bank Transfers:** 5-7 business days

If you don't see the refund after this period, please contact our support team.

@component('mail::button', ['url' => config('app.frontend_url') . '/support'])
Contact Support
@endcomponent

Thank you for your patience!

@endcomponent
