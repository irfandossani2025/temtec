<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceRequestConfirmation extends Mailable
{
    public function __construct(public ServiceRequest $serviceRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'We received your request — '.config('app.name'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.service-request-confirmation');
    }
}
