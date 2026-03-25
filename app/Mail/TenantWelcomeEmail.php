<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . ' - Your Account Details',
        );
    }

    public function content(): Content
    {
        $trialEndDate = $this->tenant->trial_ends_at->format('F j, Y');
        $daysRemaining = now()->diffInDays($this->tenant->trial_ends_at);

        return new Content(
            view: 'emails.tenant-welcome',
            with: [
                'tenant' => $this->tenant,
                'trialEndDate' => $trialEndDate,
                'daysRemaining' => $daysRemaining,
                'appName' => config('app.name'),
                'loginUrl' => config('app.url') . '/login',
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }
} 