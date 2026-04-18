@component('mail::message')
# 🚗 Your Order is On The Way!

Hi {{ $order->customer->name }},

Your order is now being delivered! 

## Delivery Information

**Order Number:** #{{ $order->order_number }}
**Status:** Out for Delivery 🚗

@if($deliveryPersonName)
**Delivery Partner:** {{ $deliveryPersonName }}
**Contact:** {{ $deliveryPersonPhone }}
@endif

**Estimated Delivery Time:** {{ $order->estimated_delivery_time ?? 20 }} minutes

**Delivery Address:**
{{ $order->delivery_address }}

## Track in Real-Time

Follow your order's progress live on the map!

@component('mail::button', ['url' => config('app.frontend_url') . '/orders/' . $order->id . '/track'])
Live Tracking
@endcomponent

Sit back and relax! Your food will arrive shortly. 🎉

@endcomponent
