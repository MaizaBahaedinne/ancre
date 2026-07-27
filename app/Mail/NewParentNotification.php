<?php

namespace App\Mail;

use App\Models\ParentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewParentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ParentModel $parent
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau parent inscrit: ' . $this->parent->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notifications.new-parent',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
