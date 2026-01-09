<!DOCTYPE html>
<html>

<head>
    <title>Your Attendance QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8fafc;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #0d9488 100%);
            color: #f8fafc;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .content p {
            color: #475569;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 16px;
        }

        .content strong {
            color: #1e293b;
        }

        .qr-code {
            margin: 30px 0;
            padding: 20px;
            background-color: #f1f5f9;
            border-radius: 12px;
            display: inline-block;
        }

        .warning {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 20px;
        }

        .footer {
            background-color: #e2e8f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>C2C Attendance System</h1>
            <p>Your Personal QR Code</p>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $data['member_name'] }}</strong>,</p>

            <p>Here is your personal QR code for <strong>{{ $data['committee_name'] }}</strong>.</p>

            <div class="qr-code">
                {!! $qrCode !!}
            </div>

            <p>Please present this QR code to register your attendance.</p>

            <div class="warning">
                ⚠️ Do not share this code with others. It is linked to your personal account.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} C2C Attendance System. All rights reserved.
        </div>
    </div>
</body>

</html>
