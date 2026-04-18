@component('mail::message')
# 🎉 Welcome to Talabat!

Hi {{ $user->name }},

Welcome to Talabat! We're thrilled to have you on board. 🚀

## What You Can Do:

✅ **Browse Restaurants** - Discover thousands of restaurants near you
✅ **Order Food** - Get your favorite meals delivered in minutes
✅ **Track Orders** - Follow your order in real-time
✅ **Save Favorites** - Bookmark your favorite restaurants and dishes
✅ **Earn Rewards** - Get special offers and discounts

## Get Started Now:

@component('mail::button', ['url' => config('app.frontend_url')])
Start Ordering
@endcomponent

## Special Offer:

As a new member, enjoy **20% OFF** your first order!

Use code: **WELCOME20**

@component('mail::button', ['url' => config('app.frontend_url') . '/restaurants'])
Explore Restaurants
@endcomponent

If you have any questions, don't hesitate to reach out to our support team.

Happy ordering! 🍽️

@endcomponent
