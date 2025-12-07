<!DOCTYPE html>
<html>

<head>
    <title>Your Attendance QR Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #1e3a8a;
            /* Brand Primary */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .qr-code {
            margin: 20px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1e3a8a;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>C2C Attendance System</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $data['member_name'] }}</strong>,</p>

            <p>Here is your personal QR code for <strong>{{ $data['committee_name'] }}</strong>.</p>

            <div class="qr-code">
                {!! $qrCode !!}
            </div>

            <p>Please present this QR code to register your attendance.</p>
            <p>Do not share this code with others.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} C2C Attendance System. All rights reserved.
        </div>
    </div>
</body>

</html>
