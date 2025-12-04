<!DOCTYPE html>
<html>

<head>
    <title>Your Attendance QR Code</title>
</head>

<body>
    <p>Hello {{ $data['member_name'] }},</p>

    <p>Attached is your QR code, which serves as your ID throughout {{ $data['session_name'] }} for attending activities
        and registering presence in {{ $data['committee_name'] }}.</p>

    <p>Please keep it safe and do not share it with others.</p>

    <p>Thank you,<br>
        Committee Management Team</p>
</body>

</html>
