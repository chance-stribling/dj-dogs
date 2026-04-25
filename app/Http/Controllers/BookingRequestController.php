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

    public function index()
    {
        return response()->json([
            'data' => BookingRequest::latest()->get(),
        ]);
    }

    public function markRead(BookingRequest $bookingRequest)
    {
        $bookingRequest->update(['read' => true]);
        return response()->json(['data' => $bookingRequest]);
    }

    public function updateStatus(BookingRequest $bookingRequest, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,declined',
        ]);
        $bookingRequest->update(['status' => $request->status]);
        return response()->json(['data' => $bookingRequest]);
    }

    public function destroy(BookingRequest $bookingRequest)
    {
        $bookingRequest->delete();
        return response()->noContent();
    }

    public function count()
    {
        return response()->json(['total' => BookingRequest::count()]);
    }
}
