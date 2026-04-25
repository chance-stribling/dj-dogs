<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandLocation extends Model
{
    protected $fillable = ['name', 'address', 'notes', 'lat', 'lng'];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];
    public function schedules()
    {
        return $this->hasMany(StandLocationSchedule::class);
    }
}
