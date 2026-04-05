<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use Illuminate\Database\Seeder;

class BookingRequestSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = [
            [
                'name'            => 'Sarah Mitchell',
                'email'           => 'sarah.mitchell@email.com',
                'phone'           => '(423) 555-0142',
                'event_type'      => 'Birthday Party',
                'event_date'      => now()->addDays(14)->toDateString(),
                'expected_guests' => 40,
                'location'        => '123 Maple St, Kingsport, TN 37660',
                'details'         => 'Throwing a 40th birthday party in my backyard. Would love to have the cart for about 3 hours. We have a covered patio so weather shouldn\'t be an issue.',
                'read'            => false,
            ],
            [
                'name'            => 'Kingsport Chamber of Commerce',
                'email'           => 'events@kingsportchamber.com',
                'phone'           => '(423) 555-0198',
                'event_type'      => 'Corporate Event',
                'event_date'      => now()->addDays(21)->toDateString(),
                'expected_guests' => 120,
                'location'        => 'downtown Kingsport, TN',
                'details'         => 'Annual business mixer downtown. Looking for a food vendor for the outdoor portion of the event. Expecting around 120 professionals. Would need you set up by 5pm.',
                'read'            => true,
            ],
            [
                'name'            => 'Tyler Brooks',
                'email'           => 'tbrooks@gmail.com',
                'phone'           => null,
                'event_type'      => 'Festival',
                'event_date'      => now()->addDays(35)->toDateString(),
                'expected_guests' => 300,
                'location'        => 'Warriors Path State Park, Kingsport, TN',
                'details'         => 'Community music festival at Warriors Path. Multi-vendor setup. We have a designated food vendor area with power hookups available. Full day event, 10am to 8pm.',
                'read'            => false,
            ],
            [
                'name'            => 'Amanda & Jake Pruitt',
                'email'           => 'amandapruitt@email.com',
                'phone'           => '(423) 555-0177',
                'event_type'      => 'Wedding',
                'event_date'      => now()->addDays(60)->toDateString(),
                'expected_guests' => 85,
                'location'        => '4500 S Scenic Hwy, Kingsport, TN 37664',
                'details'         => 'We want a late night snack station at our wedding reception. Hot dogs after the main dinner, around 9pm. Very casual vibe — this is totally our style. Can you do a 2 hour window?',
                'read'            => false,
            ],
            [
                'name'            => 'Jefferson Elementary PTA',
                'email'           => 'pta@jeffersonelem.edu',
                'phone'           => '(423) 555-0163',
                'event_type'      => 'School Event',
                'event_date'      => now()->addDays(10)->toDateString(),
                'expected_guests' => 200,
                'location'        => 'Jefferson Elementary School, Kingsport, TN',
                'details'         => 'End of year school carnival. We need a food vendor from 11am to 2pm. Lots of kids so we want to make sure the menu works for them. Budget conscious — can you do a per-dog price rather than a flat fee?',
                'read'            => true,
            ],
        ];

        BookingRequest::insert($bookings);
    }
}
