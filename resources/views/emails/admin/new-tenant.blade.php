<!DOCTYPE html>
<html>
<head>
    <title>New Tenant Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #666;
        }
        .tenant-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Tenant Registration</h1>
        </div>
        
        <div class="content">
            <p>Hello Admin,</p>
            
            <p>A new tenant has registered on {{ config('app.name') }}.</p>
            
            <div class="tenant-info">
                <h3>Tenant Details:</h3>
                <ul>
                    <li><strong>Name:</strong> {{ $tenant->name }}</li>
                    <li><strong>Email:</strong> {{ $tenant->email }}</li>
                    <li><strong>Domain:</strong> {{ $tenant->domain }}</li>
                    <li><strong>Registration Date:</strong> {{ $tenant->created_at->format('F j, Y H:i:s') }}</li>
                </ul>
            </div>
            
            <p>You can view the tenant's details by clicking the button below:</p>
            
            <p style="text-align: center;">
                <a href="{{ $adminUrl }}" class="button">View Tenant Details</a>
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 