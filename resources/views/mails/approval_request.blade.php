<!DOCTYPE html>
<html>

<head>
    <title>Approval Request</title>
</head>

<body>

    <center>
        <h2 style="padding: 23px;border: 6px red solid;">
            <a>Please Approve this request</a>
        </h2>
    </center>

    <h3>Hello,</h3>

    <p>This is to notify you that a new approval request has been sent waiting for your approval.</p>

    <p>Use the link below to view all pending requests.</p>
    <p><a href="{{ url('/api/v1/request/fetch/all') }}">Pending Requests</a></p>

    <p>
        <strong>Thanks,</strong><br>
        {{ config('app.name') }}
    </p>
</body>

</html>