<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandLocationSchedule extends Model
{
    protected $fillable = [
        'stand_location_id', 'date', 'open_time',
        'close_time', 'until_sold_out', 'is_current',
    ];

    protected $casts = [
        'date'           => 'date',
        'until_sold_out' => 'boolean',
        'is_current'     => 'boolean',
    ];

    public function standLocation()
    {
        return $this->belongsTo(StandLocation::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true)->with('standLocation');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_current', false)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->with('standLocation');
    }
}
