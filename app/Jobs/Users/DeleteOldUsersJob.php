<?php

namespace App\Jobs\Users;

use App\Services\Users\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteOldUsersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Users age in minutes
     *
     * @var int
     */
    private int $ageInMinutes;

    /**
     * Create a new job instance.
     *
     * @param int $ageInMinutes
     */
    public function __construct(int $ageInMinutes)
    {
        $this->ageInMinutes = $ageInMinutes;
    }

    /**
     * Execute the job.
     *
     * @param UserService $service
     * @return void
     */
    public function handle(UserService $service): void
    {
        $countOfDeletedUsers = $service->deleteOldUsers($this->ageInMinutes);

        Log::info($countOfDeletedUsers . ' old users deleted.');
    }
}
