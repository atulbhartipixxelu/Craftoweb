<?php

namespace App\Mail;

use App\Models\Ride;
use App\Services\RideBookingNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PassengerRideBookedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ride $ride) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name', 'HimCab').' — Ride booked (#'.$this->ride->id.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.passenger-ride-booked',
            with: [
                'ride' => $this->ride,
                'passengerToDriverWa' => RideBookingNotifier::passengerToDriverWhatsappUrl($this->ride),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
