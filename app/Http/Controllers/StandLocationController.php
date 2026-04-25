<?php

namespace App\Http\Controllers;

use App\Models\StandLocation;
use App\Services\GeocoderService;
use Illuminate\Http\Request;

class StandLocationController extends Controller
{
    public function __construct(protected GeocoderService $geocoder) {}

    public function index()
    {
        return response()->json(['data' => StandLocation::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'notes'   => 'nullable|string|max:255',
        ]);

        $coords = $this->geocoder->geocode($data['address']);
        if ($coords) {
            $data['lat'] = $coords['lat'];
            $data['lng'] = $coords['lng'];
        }

        return response()->json(['data' => StandLocation::create($data)], 201);
    }

    public function update(Request $request, StandLocation $standLocation)
    {
        $data = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'notes'   => 'nullable|string|max:255',
        ]);

        if (isset($data['address']) && $data['address'] !== $standLocation->address) {
            $coords = $this->geocoder->geocode($data['address']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $standLocation->update($data);
        return response()->json(['data' => $standLocation]);
    }

    public function destroy(StandLocation $standLocation)
    {
        $standLocation->delete();
        return response()->noContent();
    }
}
