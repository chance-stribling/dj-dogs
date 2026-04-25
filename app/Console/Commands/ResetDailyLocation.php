<?php

namespace App\Console\Commands;

use App\Models\StandLocationSchedule;
use Illuminate\Console\Command;

class ResetDailyLocation extends Command
{
    protected $signature   = 'locations:reset-daily';
    protected $description = 'Clear current location at midnight, auto-set next day if scheduled';

    public function handle()
    {
        // Clear all current flags
        StandLocationSchedule::where('is_current', true)
            ->update(['is_current' => false]);

        // Check if anything is scheduled for today (the new day after midnight)
        $todaySchedule = StandLocationSchedule::whereDate('date', now()->toDateString())
            ->first();

        if ($todaySchedule) {
            $todaySchedule->update(['is_current' => true]);
            $this->info("Auto-set: {$todaySchedule->standLocation->name}");
        } else {
            $this->info('No schedule for today, current location cleared.');
        }
    }
}
