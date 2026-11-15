<?php

namespace App\Filament\Pages;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OperationsMonitor extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.operations-monitor';

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.operations_monitor.navigation');
    }

    public function getTitle(): string
    {
        return __('admin.pages.operations_monitor.title');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Gather sanitized operational, queue, and Release 1 funnel metrics without exposing secrets.
     *
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        $totalCouples = User::where('role', 'user')->count();
        $activatedCouples = User::where('role', 'user')
            ->whereHas('invitations', fn ($q) => $q->where('status', 'published'))
            ->count();

        $publishedInvitations = Invitation::where('status', 'published')->count();
        $draftInvitations = Invitation::where('status', 'draft')->count();
        $archivedInvitations = Invitation::where('status', 'archived')->count();
        $totalRsvps = RsvpResponse::count();
        $confirmedHeads = (int) RsvpResponse::where('attendance_status', 'attending')->sum('guest_count')
            + (int) RsvpResponse::where('attendance_status', 'attending')->sum('children_count');

        $avgRsvpsPerPublished = $publishedInvitations > 0
            ? round($totalRsvps / $publishedInvitations, 1)
            : 0.0;

        $pendingJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedJobsCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $notificationsCount = Schema::hasTable('notifications') ? DB::table('notifications')->count() : 0;

        $recentFailedJobs = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(15)
                ->get(['id', 'connection', 'queue', 'failed_at', 'exception'])
                ->map(fn (object $row): array => [
                    'id' => $row->id,
                    'connection' => $row->connection,
                    'queue' => $row->queue,
                    'failed_at' => (string) $row->failed_at,
                    'error_summary' => Str::limit(strtok((string) $row->exception, "\n") ?: 'Unknown error', 180),
                ])
                ->all()
            : [];

        return [
            'funnel' => [
                'total_couples' => $totalCouples,
                'activated_couples' => $activatedCouples,
                'activation_rate' => $totalCouples > 0 ? round(($activatedCouples / $totalCouples) * 100, 1) : 0.0,
                'published_invitations' => $publishedInvitations,
                'draft_invitations' => $draftInvitations,
                'archived_invitations' => $archivedInvitations,
                'total_rsvps' => $totalRsvps,
                'confirmed_heads' => $confirmedHeads,
                'avg_rsvps_per_published' => $avgRsvpsPerPublished,
            ],
            'delivery' => [
                'mail_mailer' => (string) config('mail.default', 'log'),
                'queue_connection' => (string) config('queue.default', 'sync'),
                'resend_configured' => filled(config('services.resend.key')),
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobsCount,
                'stored_notifications' => $notificationsCount,
            ],
            'recentFailedJobs' => $recentFailedJobs,
        ];
    }
}
