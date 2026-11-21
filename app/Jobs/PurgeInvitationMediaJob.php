<?php

namespace App\Jobs;

use App\Models\Invitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurgeInvitationMediaJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /**
     * @var array<int, int>
     */
    public array $backoff = [60, 300, 900, 3600];

    /**
     * Create a new job instance.
     *
     * @param  array<int, string>  $paths
     */
    public function __construct(public array $paths) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $failedPaths = Invitation::purgeMediaPaths($this->paths, dispatchRetryOnFailure: false);

        if ($failedPaths !== []) {
            throw new \RuntimeException(
                'Failed to purge invitation media paths: '.implode(', ', $failedPaths)
            );
        }
    }
}
