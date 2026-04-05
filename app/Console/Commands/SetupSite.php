<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupSite extends Command
{
    protected $signature = 'site:setup
                            {--fresh : Drop all tables and re-migrate}';

    protected $description = 'Run all migrations and seed the site with initial data';

    public function handle(): void
    {
        $this->info('Setting up DJ\'s Hot Dog Cart...');
        $this->newLine();

        if ($this->option('fresh')) {
            $this->info('Dropping all tables...');
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        $this->newLine();

        // Seed the database
        $this->call('db:seed', ['--class' => 'MenuItemSeeder', '--force' => true]);
        $this->call('db:seed', ['--class' => 'StandLocationSeeder', '--force' => true]);
        $this->call('db:seed', ['--class' => 'BookingRequestSeeder', '--force' => true]);

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
