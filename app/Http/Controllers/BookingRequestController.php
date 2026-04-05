<?php

namespace App\Http\Controllers;

use App\Mail\BookingMail;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'event_type'      => 'required|string|max:255',
            'event_date'      => 'nullable|date',
            'expected_guests' => 'nullable|integer|min:1',
            'location'        => 'nullable|string|max:255',
            'details'         => 'required|string|max:3000',
        ]);

        $bookingRequest = BookingRequest::create($validated);

        Mail::to('dj@djshotdogcart.com')
            ->send(new BookingMail($bookingRequest->toArray()));

        return back()->with('success', 'Booking request sent! We\'ll be in touch soon.');
    }
}
