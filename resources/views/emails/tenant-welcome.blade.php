<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to {{ $appName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-box {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ $appName }}!</h1>
    </div>

    <div class="content">
        <p>Dear {{ $tenant->name }},</p>

        <p>Thank you for choosing {{ $appName }}! We're excited to have you on board.</p>

        <div class="status-box">
            <h3>Your Account Status</h3>
            <p><strong>Current Status:</strong> {{ ucfirst($tenant->status) }}</p>
            <p><strong>Trial Period:</strong> {{ $daysRemaining }} days remaining</p>
            <p><strong>Trial Ends:</strong> {{ $trialEndDate }}</p>
        </div>

        <p>Your account is currently in trial mode. During this period, you have full access to all features. To continue using our services after the trial period, please ensure your account is approved by our team.</p>

        <p>Here's what you can do next:</p>
        <ul>
            <li>Complete your profile setup</li>
            <li>Explore our features and documentation</li>
            <li>Set up your first project</li>
        </ul>

        <p style="text-align: center;">
            <a href="http://{{$tenant->domains()->first()->domain}}:8000/login" class="button">Access Your Account</a>
        </p>

        <p>If you have any questions or need assistance, our support team is here to help. You can reach us at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>

        <p>Best regards,<br>The {{ $appName }} Team</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $tenant->email }}. If you didn't create an account with {{ $appName }}, please ignore this email.</p>
        <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
    </div>
</body>
</html> 