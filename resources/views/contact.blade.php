<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #2C2623; padding: 32px; }
        .label { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .value { font-size: 16px; margin-bottom: 24px; }
        .message { background: #F5F4F0; padding: 16px; border-radius: 8px; }
        h2 { color: #F97316; }
    </style>
</head>
<body>
<h2>New Contact Message — Big J's Hot Dog Cart</h2>

<p class="label">Name</p>
<p class="value">{{ $data['name'] }}</p>

<p class="label">Email</p>
<p class="value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></p>

@if(!empty($data['phone']))
    <p class="label">Phone</p>
    <p class="value">{{ $data['phone'] }}</p>
@endif

<p class="label">Message</p>
<div class="message">{{ $data['message'] }}</div>
</body>
</html>
