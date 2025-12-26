<?php

namespace App\Console\Commands;

use App\Models\PendingRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredPendingRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending-registrations:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired pending registration records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('Starting cleanup of expired pending registrations');

        $deletedCount = PendingRegistration::expired()->delete();

        Log::info('Cleanup completed', [
            'deleted_count' => $deletedCount,
        ]);

        $this->info("Cleaned up {$deletedCount} expired pending registration(s).");

        return Command::SUCCESS;
    }
}
