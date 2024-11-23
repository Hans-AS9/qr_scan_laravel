<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ParticipantRegistered extends Mailable
{
    use Queueable, SerializesModels;
    public $participant;
    public $qrcode;
    public $path;

    /**
     * Create a new message instance.
     */
    public function __construct($participant, $qrcode = null, $path = null)
    {
        // dd($participant, $qrcode, $path);

        $this->participant = $participant;
        $this->qrcode = $qrcode;
        $this->path = $path;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address("noreply@meetap.com", "MeetAp Mail"),
            subject: 'Register Participant',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.participant_registered',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->path == null) {
            return [];
        } else {
            return [
                attachment::fromPath($this->path),
            ];
        }
    }
}