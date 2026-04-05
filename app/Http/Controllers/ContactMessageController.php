<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);
        ContactMessage::create($validated);

        Mail::to('dj@djshotdogcart.com')->send(new ContactMail($validated));

        return redirect(url()->previous() . '#contact')->with('success', 'Message sent! We\'ll get back to you faster than we can grill a dog.');
    }

    public function index()
    {
        return ContactMessage::latest()->paginate(20);
    }

    public function markRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read' => true]);
        return $contactMessage;
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return response()->noContent();
    }
}
