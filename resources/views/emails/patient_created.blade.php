<!DOCTYPE html>
<html>
<head>
    <title>New Patient Created</title>
</head>
<body>
    <h1>New Patient Created</h1>
    <p>Dear {{ $patient->full_name }},</p>
    <p>Your patient profile has been created successfully. Below are your details:</p>
    <ul>
        <li><strong>Patient Name:</strong> {{ $patient->full_name }}</li>
        <li><strong>Patient Unique ID:</strong> {{ $patient->pat_unique_id }}</li>
        <li><strong>Email:</strong> {{ $patient->email }}</li>
    </ul>
    <p>The profile was created by:</p>
    <ul>
        <li><strong>Creator Name:</strong> {{ $creator->name }}</li>
        <li><strong>Creator Email:</strong> {{ $creator->email }}</li>
    </ul>
    <p>If you have any questions, please contact us.</p>
    <p>Thank you!</p>
</body>
</html>