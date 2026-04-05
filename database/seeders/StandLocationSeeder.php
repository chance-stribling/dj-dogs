<?php

namespace Database\Seeders;

use App\Models\StandLocation;
use Illuminate\Database\Seeder;

class StandLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name'       => 'Clearwater Beach Pier',
                'address'    => 'Clearwater Beach Pier, Clearwater, FL',
                'lat'        => 27.9772,
                'lng'        => -82.8279,
                'hours'      => '10am - 6pm',
                'notes'      => 'Beachside parking lot',
                'date'       => now()->toDateString(),
                'is_current' => true,
            ],
            [
                'name'       => 'Downtown St. Pete',
                'address'    => 'Downtown St. Pete, FL',
                'lat'        => 27.7731,
                'lng'        => -82.6400,
                'hours'      => '11am - 8pm',
                'notes'      => null,
                'date'       => now()->addDay()->toDateString(),
                'is_current' => false,
            ],
            [
                'name'       => 'Tampa Riverwalk',
                'address'    => 'Tampa Riverwalk, Tampa, FL',
                'lat'        => 27.9478,
                'lng'        => -82.4584,
                'hours'      => '12pm - 7pm',
                'notes'      => 'Near the Amalie Arena entrance',
                'date'       => now()->addDays(2)->toDateString(),
                'is_current' => false,
            ],
        ];

        StandLocation::insert($locations);
    }
}
