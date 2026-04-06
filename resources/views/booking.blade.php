<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #2C2623; padding: 32px; }
        .label { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .value { font-size: 16px; margin-bottom: 20px; }
        .details { background: #F5F4F0; padding: 16px; border-radius: 8px; }
        h2 { color: #F97316; }
    </style>
</head>
<body>
<h2>Booking Request — Big J's Hot Dog Cart</h2>

<p class="label">Name</p>
<p class="value">{{ $data['name'] }}</p>

<p class="label">Email</p>
<p class="value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></p>

@if(!empty($data['phone']))
    <p class="label">Phone</p>
    <p class="value">{{ $data['phone'] }}</p>
@endif

<p class="label">Event Type</p>
<p class="value">{{ $data['event_type'] }}</p>

@if(!empty($data['event_date']))
    <p class="label">Event Date</p>
    <p class="value">{{ \Carbon\Carbon::parse($data['event_date'])->format('F j, Y') }}</p>
@endif

@if(!empty($data['expected_guests']))
    <p class="label">Expected Guests</p>
    <p class="value">{{ $data['expected_guests'] }}</p>
@endif

@if(!empty($data['location']))
    <p class="label">Location</p>
    <p class="value">{{ $data['location'] }}</p>
@endif

<p class="label">Details</p>
<div class="details">{{ $data['details'] }}</div>
</body>
</html>
