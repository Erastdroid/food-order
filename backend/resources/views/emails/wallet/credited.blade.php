@component('mail::message')
# 💰 Your Wallet Has Been Credited!

Hi {{ $wallet->user->name }},

Great news! Your Talabat wallet has been credited.

## Wallet Details

**Amount Credited:** +${{ number_format($amount, 2) }}
**Reason:** {{ $description }}
**New Balance:** ${{ number_format($wallet->balance, 2) }}

You can now use this balance for your next orders!

@component('mail::button', ['url' => config('app.frontend_url') . '/wallet'])
View Wallet
@endcomponent

Thank you for using Talabat! 🎉

@endcomponent
