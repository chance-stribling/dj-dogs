<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name'    => 'Jake Turner',
                'email'   => 'jake.turner@email.com',
                'phone'   => '(423) 555-0182',
                'message' => 'Hey! Just wanted to say your Chicago Dog is absolutely incredible. Best hot dog I\'ve had outside of Chicago. Will you be at the Kingsport Farmers Market next Saturday?',
                'read'    => false,
            ],
            [
                'name'    => 'Sarah Mitchell',
                'email'   => 'sarah.m@gmail.com',
                'phone'   => null,
                'message' => 'Hi there! I\'m organizing a company picnic for about 50 people on June 14th at Bays Mountain Park. Would you be available to cater? We\'d love to have Big J\'s there!',
                'read'    => false,
            ],
            [
                'name'    => 'Marcus Webb',
                'email'   => 'marcuswebb@outlook.com',
                'phone'   => '(423) 555-0347',
                'message' => 'Do you do birthday parties? My son is turning 10 and he is obsessed with your hot dogs. We\'re looking at late July.',
                'read'    => false,
            ],
            [
                'name'    => 'Linda Okafor',
                'email'   => 'linda.okafor@yahoo.com',
                'phone'   => null,
                'message' => 'Quick question — do any of your dogs have gluten free bun options? My husband has celiac and we drove by your cart yesterday and it smelled amazing. Would love to bring him!',
                'read'    => true,
            ],
            [
                'name'    => 'Derek Holt',
                'email'   => 'dholt@gmail.com',
                'phone'   => '(423) 555-0091',
                'message' => 'Yo just wanted to say I found you guys through Instagram and came out last Thursday. That Fire Dog with the jalapeño relish is INSANE. Please never take it off the menu.',
                'read'    => true,
            ],
        ];

        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
    }
}
