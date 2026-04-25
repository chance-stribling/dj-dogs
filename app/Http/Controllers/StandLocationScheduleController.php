<?php

namespace App\Http\Controllers;

use App\Models\StandLocationSchedule;
use Illuminate\Http\Request;

class StandLocationScheduleController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => StandLocationSchedule::with('standLocation')->orderBy('date')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stand_location_id' => 'required|exists:stand_locations,id',
            'date'              => 'required|date',
            'open_time'         => 'nullable|date_format:H:i',
            'close_time'        => 'nullable|date_format:H:i',
            'until_sold_out'    => 'boolean',
            'is_current'        => 'boolean',
        ]);

        if (!empty($data['is_current'])) {
            StandLocationSchedule::where('is_current', true)->update(['is_current' => false]);
        }

        $schedule = StandLocationSchedule::create($data);
        $schedule->load('standLocation');
        return response()->json(['data' => $schedule], 201);
    }

    public function update(Request $request, StandLocationSchedule $standLocationSchedule)
    {
        $data = $request->validate([
            'stand_location_id' => 'sometimes|exists:stand_locations,id',
            'date'              => 'sometimes|date',
            'open_time'         => 'nullable|date_format:H:i',
            'close_time'        => 'nullable|date_format:H:i',
            'until_sold_out'    => 'boolean',
            'is_current'        => 'boolean',
        ]);

        if (!empty($data['is_current'])) {
            StandLocationSchedule::where('is_current', true)
                ->where('id', '!=', $standLocationSchedule->id)
                ->update(['is_current' => false]);
        }

        $standLocationSchedule->update($data);
        $standLocationSchedule->load('standLocation');
        return response()->json(['data' => $standLocationSchedule]);
    }

    public function destroy(StandLocationSchedule $standLocationSchedule)
    {
        $standLocationSchedule->delete();
        return response()->noContent();
    }

    public function setCurrent(StandLocationSchedule $standLocationSchedule)
    {
        StandLocationSchedule::where('is_current', true)->update(['is_current' => false]);
        $standLocationSchedule->update(['is_current' => true]);
        $standLocationSchedule->load('standLocation');
        return response()->json(['data' => $standLocationSchedule]);
    }
}
