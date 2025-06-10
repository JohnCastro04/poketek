<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class RandomProfileImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:random-pictures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign random profile pictures to users who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('profile_picture')
                    ->orWhere('profile_picture', 0)
                    ->get();

        if ($users->isEmpty()) {
            $this->info('No users found without profile pictures.');
            return;
        }

        $this->info("Found {$users->count()} users without profile pictures.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $user->assignRandomProfilePicture();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Random profile pictures assigned successfully!');
    }
}