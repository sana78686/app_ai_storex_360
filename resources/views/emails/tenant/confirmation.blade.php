<!DOCTYPE html>
<html>
<head>
    <title>Account Confirmation</title>
</head>
<body>
    <p>Hello {{ $tenantName }},</p>

    <p>Thank you for registering with {{ config('app.name') }}! To activate your account and start your free trial, please confirm your email address by entering the following 6-digit code:</p>

    <h2><strong>{{ $confirmationToken }}</strong></h2>

    <p>This code will expire in 30 minutes. If you did not register for a {{ config('app.name') }} account, please disregard this email.</p>

    <p>Best regards,</p>
    <p>The {{ config('app.name') }} Team</p>
</body>
</html>
