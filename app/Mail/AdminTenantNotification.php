<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminTenantNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function build()
    {
        return $this->subject('New Tenant Registration')
                    ->view('emails.admin.new-tenant')
                    ->with([
                        'tenant' => $this->tenant,
                        'adminUrl' => config('app.url') . '/admin/tenants/' . $this->tenant->id
                    ]);
    }
} 