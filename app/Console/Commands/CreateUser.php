<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    protected $signature = 'user:create
                            {name : The user\'s name}
                            {email : The user\'s email}
                            {--password= : Password (auto-generated if omitted)}
                            {--admin : Mark user as admin}';

    protected $description = 'Create a new user account';

    public function handle()
    {
        $password = $this->option('password') ?? Str::random(16);

        $user = User::create([
            'name'     => $this->argument('name'),
            'email'    => $this->argument('email'),
            'password' => Hash::make($password),
        ]);

        // If you have an is_admin column:
        // if ($this->option('admin')) {
        //     $user->update(['is_admin' => true]);
        // }

        $this->info("User created successfully!");
        $this->table(['Field', 'Value'], [
            ['Name',  $user->name],
            ['Email', $user->email],
            ['Password', $password],
        ]);

        return Command::SUCCESS;
    }
}
