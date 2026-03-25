<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

   public $tenant;
   public $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
   public function __construct(Tenant $tenant, $otp)
{
    $this->tenant = $tenant;
    $this->otp = $otp; // store OTP separately
}

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Confirm Your SaleTodayStore Account',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.tenant.confirmation',
            with: [
            'confirmationToken' => $this->otp,
            'tenantName' => $this->tenant->name ?? 'Tenant', // fallback if name null
        ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
