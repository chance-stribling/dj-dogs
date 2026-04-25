<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocoderService
{
    public function geocode(string $address): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name') . '/1.0 (' . env('NOMINATIM_EMAIL') . ')',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q'              => $address,
                'format'         => 'json',
                'limit'          => 1,
                'addressdetails' => 0,
            ]);

            $results = $response->json();

            if (!empty($results[0])) {
                return [
                    'lat' => (float) $results[0]['lat'],
                    'lng' => (float) $results[0]['lon'],
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding failed for address: ' . $address, ['error' => $e->getMessage()]);
        }

        return null;
    }
}
