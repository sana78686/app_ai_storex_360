<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */
    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */
    'mailers' => [

        // SMTP (Gmail, Outlook, etc.)
        'smtp' => [
            'transport' => 'smtp',
            'scheme'    => env('MAIL_SCHEME'),
            'url'       => env('MAIL_URL'),
            'host'      => env('MAIL_HOST', '127.0.0.1'),
            'port'      => env('MAIL_PORT', 587),
            'username'  => env('MAIL_USERNAME'),
            'password'  => env('MAIL_PASSWORD'),
            'timeout'   => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        // Mailgun – required for your tenant dynamic config
        'mailgun' => [
            'transport' => 'mailgun',
        ],

        // AWS SES
        'ses' => [
            'transport' => 'ses',
        ],

        // Postmark
        'postmark' => [
            'transport' => 'postmark',
        ],

        // Resend API
        'resend' => [
            'transport' => 'resend',
        ],

        // Sendmail
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        // Log transport
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        // Array transport (for tests)
        'array' => [
            'transport' => 'array',
        ],

        // Failover mailer
        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp', 'log'],
        ],

        // Round-robin mailer
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['ses', 'postmark'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'saifullahkhalid777888@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'Admin | AI StoreX 360'),
    ],

    'support_address' => env('SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),

    // Custom additional config used in your app
    'admin_to' => env('MAIL_USERNAME'),
];
