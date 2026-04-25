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
                'name'            => 'Ashley Perkins',
                'email'           => 'ashley.perkins@gmail.com',
                'phone'           => '(423) 555-0214',
                'event_type'      => 'Birthday Party',
                'event_date'      => '2026-05-17',
                'expected_guests' => 30,
                'location'        => 'Kingsport, TN',
                'details'         => 'Backyard party, would love the full setup if possible. Budget is flexible.',
                'read'            => false,
            ],
            [
                'name'            => 'Riverfront Music Festival',
                'email'           => 'vendors@riverfrontfest.com',
                'phone'           => '(423) 555-0388',
                'event_type'      => 'Festival / Vendor Spot',
                'event_date'      => '2026-06-07',
                'expected_guests' => 1000,
                'location'        => 'Kingsport Riverfront Park',
                'details'         => 'Annual festival, expected attendance 800-1200 over two days.',
                'read'            => false,
            ],
            [
                'name'            => 'TechBridge Solutions',
                'email'           => 'events@techbridge.com',
                'phone'           => null,
                'event_type'      => 'Corporate Lunch',
                'event_date'      => '2026-05-29',
                'expected_guests' => 60,
                'location'        => 'TechBridge Office, Kingsport TN',
                'details'         => 'Quarterly office lunch. Looking for a regular vendor — this would be a trial run.',
                'read'            => false,
            ],
            [
                'name'            => 'Connor & Brianna Walsh',
                'email'           => 'walshwedding2026@gmail.com',
                'phone'           => '(423) 555-0561',
                'event_type'      => 'Wedding Reception',
                'event_date'      => '2026-07-12',
                'expected_guests' => 120,
                'location'        => 'The Ridges Event Center, Kingsport TN',
                'details'         => 'Late night snack station for our reception. We saw you at the farmers market and fell in love with the concept!',
                'read'            => true,
            ],
            [
                'name'            => 'Dobyns-Bennett Booster Club',
                'email'           => 'dbboosterclub@yahoo.com',
                'phone'           => '(423) 555-0743',
                'event_type'      => 'School Fundraiser',
                'event_date'      => '2026-05-09',
                'expected_guests' => 200,
                'location'        => 'Dobyns-Bennett High School, Kingsport TN',
                'details'         => 'Football season kickoff fundraiser. Need admin approval first but wanted to get on your radar early.',
                'read'            => true,
            ],
        ];

        foreach ($bookings as $booking) {
            BookingRequest::create($booking);
        }
    }
}
