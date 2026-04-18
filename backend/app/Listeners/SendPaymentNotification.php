<?php

namespace App\Listeners;

use App\Mail\PaymentConfirmationMail;
use App\Mail\RefundProcessedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentNotification implements ShouldQueue
{
    public function handle($event): void
    {
        if (method_exists($event, 'payment')) {
            $payment = $event->payment();

            if ($payment->status === 'completed') {
                Mail::to($payment->user->email)->queue(new PaymentConfirmationMail($payment));
            } elseif ($payment->status === 'refunded') {
                Mail::to($payment->user->email)->queue(new RefundProcessedMail($payment));
            }
        }
    }
}
