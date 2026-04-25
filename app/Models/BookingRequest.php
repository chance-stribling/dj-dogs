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
        'status',
    ];

    protected $casts = [
        'event_date'      => 'date:Y-m-d',
        'expected_guests' => 'integer',
        'read'            => 'boolean',
    ];
}
