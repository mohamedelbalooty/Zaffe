<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Invitation;
use App\Services\InvitationImageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessInvitationImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        public readonly int $invitationId,
        public readonly string $tempDisk,
        public readonly string $tempPath,
        public readonly string $variant,
        public readonly ?int $sortOrder = null,
    ) {}

    public function handle(InvitationImageService $imageService): void
    {
        $invitation = Invitation::find($this->invitationId);
        if (! $invitation) {
            $imageService->deleteTempFile($this->tempDisk, $this->tempPath);

            return;
        }

        if ($this->variant === 'cover') {
            $imageService->processCoverFromTemp($invitation, $this->tempDisk, $this->tempPath);

            return;
        }

        $imageService->processGalleryFromTemp($invitation, $this->tempDisk, $this->tempPath, $this->sortOrder);
    }

    public function failed(?Throwable $exception): void
    {
        app(InvitationImageService::class)->deleteTempFile($this->tempDisk, $this->tempPath);
    }
}
