<?php

namespace App\Mail;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletCreditedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Wallet $wallet,
        public float $amount,
        public string $description
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: "💰 Your Wallet Has Been Credited!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet.credited',
            with: [
                'wallet' => $this->wallet,
                'amount' => $this->amount,
                'description' => $this->description,
            ],
        );
    }
}
