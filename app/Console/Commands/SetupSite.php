<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupSite extends Command
{
    protected $signature   = 'site:setup {--fresh : Drop all tables and re-migrate}';
    protected $description = 'Run all migrations and seed the site with initial data';

    public function handle(): void
    {
        $this->info('Setting up DJ\'s Hot Dog Cart...');
        $this->newLine();

        if ($this->option('fresh')) {
            if ($this->confirm('This will drop all tables. Are you sure?')) {
                $this->call('migrate:fresh');
            } else {
                $this->warn('Skipping fresh migration.');
                return;
            }
        } else {
            $this->call('migrate');
        }

        $this->newLine();
        $this->call('db:seed', ['--class' => 'MenuItemSeeder']);
        $this->call('db:seed', ['--class' => 'StandLocationSeeder']);
        $this->call('db:seed', ['--class' => 'BookingRequestSeeder']);
        $this->newLine();
        $this->info('Clearing caches...');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        $this->newLine();
        $this->info('✔ Site setup complete!');
        $this->table(
            ['Seeded', 'Count'],
            [
                ['Menu Items',       \App\Models\MenuItem::count()],
                ['Stand Locations',  \App\Models\StandLocation::count()],
                ['Booking Requests', \App\Models\BookingRequest::count()],
            ]
        );
    }
}
