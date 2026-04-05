<?php

namespace App\Http\Controllers;

use App\Models\StandLocation;
use Illuminate\Http\Request;

class StandLocationController extends Controller
{
    public function index()
    {
        return response()->json([
            'current'  => StandLocation::current()->first(),
            'upcoming' => StandLocation::upcoming()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string|max:255',
            'lat'        => 'required|numeric',
            'lng'        => 'required|numeric',
            'hours'      => 'nullable|string|max:255',
            'notes'      => 'nullable|string|max:255',
            'date'       => 'required|date',
            'is_current' => 'boolean',
        ]);

        // Only one location can be current at a time
        if (!empty($validated['is_current'])) {
            StandLocation::where('is_current', true)->update(['is_current' => false]);
        }

        return StandLocation::create($validated);
    }

    public function update(Request $request, StandLocation $standLocation)
    {
        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'address'    => 'sometimes|string|max:255',
            'lat'        => 'sometimes|numeric',
            'lng'        => 'sometimes|numeric',
            'hours'      => 'nullable|string|max:255',
            'notes'      => 'nullable|string|max:255',
            'date'       => 'sometimes|date',
            'is_current' => 'sometimes|boolean',
        ]);

        if (!empty($validated['is_current'])) {
            StandLocation::where('is_current', true)
                ->where('id', '!=', $standLocation->id)
                ->update(['is_current' => false]);
        }

        $standLocation->update($validated);

        return $standLocation;
    }

    public function destroy(StandLocation $standLocation)
    {
        $standLocation->delete();

        return response()->noContent();
    }
}
