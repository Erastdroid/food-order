<?php

namespace App\Providers;

use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderNotification;
use App\Listeners\SendPaymentNotification;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderStatusChanged::class => [
            SendOrderNotification::class,
        ],
        Registered::class => [
            SendWelcomeEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
