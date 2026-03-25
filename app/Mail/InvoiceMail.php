<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $downloadUrl;
    public $orderUrl;

    public function __construct($invoice, string $downloadUrl, string $orderUrl)
    {
        $this->invoice     = $invoice;
        $this->downloadUrl = $downloadUrl;
        $this->orderUrl    = $orderUrl;
    }

    public function build()
    {
        return $this->subject('Invoice ' . $this->invoice->invoice_number)
            ->view('emails.invoice'); // ❗ plain blade
    }
}
