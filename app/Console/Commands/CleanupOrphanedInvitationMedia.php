<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupOrphanedInvitationMedia extends Command
{
    /**
     * @var array<int, string>
     */
    private const MEDIA_DIRECTORIES = [
        'invitations/covers',
        'invitations/gallery',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zaffe:cleanup-orphaned-media
                            {--dry-run : List orphaned files without deleting them}
                            {--min-age=60 : Minimum file age in minutes before deletion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orphaned invitation cover and gallery files that are no longer referenced in the database';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $minAgeMinutes = max(0, (int) $this->option('min-age'));
        $cutoffTimestamp = now()->subMinutes($minAgeMinutes)->getTimestamp();

        $referencedPaths = $this->collectReferencedPaths();
        $disk = Storage::disk('public');

        $deletedCount = 0;
        $candidateCount = 0;
        $failureCount = 0;

        foreach (self::MEDIA_DIRECTORIES as $directory) {
            foreach ($disk->allFiles($directory) as $file) {
                $normalized = ltrim($file, '/');
                if ($normalized === '' || isset($referencedPaths[$normalized])) {
                    continue;
                }

                try {
                    if ($disk->lastModified($normalized) > $cutoffTimestamp) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Unable to inspect orphaned media file timestamp; skipping.', [
                        'path' => $normalized,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                $candidateCount++;

                if ($dryRun) {
                    $this->line("[dry-run] Orphaned file: {$normalized}");

                    continue;
                }

                try {
                    if ($disk->delete($normalized)) {
                        $deletedCount++;
                    } else {
                        $failureCount++;
                        Log::warning('Failed to delete orphaned invitation media file.', [
                            'path' => $normalized,
                            'reason' => 'delete_returned_false',
                        ]);
                    }
                } catch (\Throwable $e) {
                    $failureCount++;
                    Log::warning('Failed to delete orphaned invitation media file.', [
                        'path' => $normalized,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete: {$candidateCount} orphaned invitation media file(s) identified.");
        } else {
            $this->info("Cleanup complete: deleted {$deletedCount} orphaned invitation media file(s) ({$failureCount} failure(s)).");
        }

        return $failureCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<string, true>
     */
    private function collectReferencedPaths(): array
    {
        $referenced = [];

        foreach (Invitation::query()->whereNotNull('cover_photo')->pluck('cover_photo') as $rawPath) {
            $resolved = Invitation::resolvePublicDiskPath(is_string($rawPath) ? $rawPath : null);
            if ($resolved !== null) {
                $referenced[$resolved] = true;
            }
        }

        foreach (InvitationPhoto::query()->whereNotNull('image_path')->pluck('image_path') as $rawPath) {
            $resolved = Invitation::resolvePublicDiskPath(is_string($rawPath) ? $rawPath : null);
            if ($resolved !== null) {
                $referenced[$resolved] = true;
            }
        }

        return $referenced;
    }
}
