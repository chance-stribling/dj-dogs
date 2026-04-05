<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_type',
        'event_date',
        'expected_guests',
        'location',
        'details',
        'read',
    ];

    protected $casts = [
        'event_date'      => 'date',
        'expected_guests' => 'integer',
        'read'            => 'boolean',
    ];
}
