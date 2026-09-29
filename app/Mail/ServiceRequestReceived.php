<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceRequestReceived extends Mailable
{
    public function __construct(public ServiceRequest $serviceRequest) {}

    public function envelope(): Envelope
    {
        $user = $this->serviceRequest->user;

        return new Envelope(
            replyTo: [new Address($user->email, $user->name)],
            subject: "New service request #{$this->serviceRequest->id} from ".($user->company_name ?: $user->name),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.service-request-received');
    }
}
