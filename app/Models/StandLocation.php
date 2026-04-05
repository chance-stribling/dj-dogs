<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandLocation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'lat',
        'lng',
        'hours',
        'notes',
        'date',
        'is_current',
    ];

    protected $casts = [
        'lat'        => 'float',
        'lng'        => 'float',
        'date'       => 'date',
        'is_current' => 'boolean',
    ];

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_current', false)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date');
    }
}
