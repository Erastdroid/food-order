@component('mail::message')
# ✅ Your Order Has Been Confirmed!

Hi {{ $order->customer->name }},

Great news! Your order from **{{ $restaurant->name }}** has been confirmed and is being prepared.

## Order Details

**Order Number:** #{{ $order->order_number }}
**Restaurant:** {{ $restaurant->name }}
**Status:** Confirmed ✅

### Items Ordered:
@component('mail::table')
| Item | Quantity | Price | Total |
|------|----------|-------|-------|
@foreach($items as $item)
| {{ $item->menu_item->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} | ${{ number_format($item->total, 2) }} |
@endforeach
|  |  | **Subtotal** | **${{ number_format($order->subtotal, 2) }}** |
|  |  | **Delivery Fee** | **${{ number_format($order->delivery_fee, 2) }}** |
|  |  | **Tax** | **${{ number_format($order->tax, 2) }}** |
|  |  | **Total** | **${{ number_format($order->total_amount, 2) }}** |
@endcomponent

### Delivery Address:
{{ $order->delivery_address }}

### Estimated Delivery Time:
⏱️ **{{ $order->estimated_delivery_time ?? 30 }} minutes**

## Next Steps:
1. 👨‍🍳 Your food is being prepared
2. 📦 We'll notify you when it's ready for pickup
3. 🚗 A delivery partner will be assigned shortly
4. ✨ Track your order in real-time in the app

@component('mail::button', ['url' => config('app.frontend_url') . '/orders/' . $order->id . '/track'])
Track Your Order
@endcomponent

Thank you for ordering with Talabat!

**Questions?** Reply to this email or contact our support team.

@endcomponent
