<?php

namespace App\Filament\Pages;

use App\Models\FunnelDailyMetric;
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
        $publishedRsvps = RsvpResponse::whereHas('invitation', fn ($q) => $q->where('status', 'published'))->count();
        $confirmedHeads = (int) RsvpResponse::where('attendance_status', 'attending')->sum('guest_count')
            + (int) RsvpResponse::where('attendance_status', 'attending')->sum('children_count');

        $avgRsvpsPerPublished = $publishedInvitations > 0
            ? round($publishedRsvps / $publishedInvitations, 1)
            : 0.0;

        $since30Days = now()->subDays(29)->startOfDay();

        // KPI-01: Creation-Start -> Published Funnel
        $creationStarts = FunnelDailyMetric::sumForKey(FunnelDailyMetric::KEY_CREATION_START);
        $creationStarts30d = FunnelDailyMetric::sumForKey(FunnelDailyMetric::KEY_CREATION_START, $since30Days);
        $published30d = Invitation::where('status', 'published')
            ->where('created_at', '>=', $since30Days)
            ->count();

        // KPI-02: Published -> Shared (>= 10 views OR >= 1 RSVP)
        $sharedPublishedCount = Invitation::where('status', 'published')
            ->where(fn ($q) => $q->where('view_count', '>=', 10)->orWhereHas('rsvps'))
            ->count();
        $sharedPublishedRate = $publishedInvitations > 0
            ? round(($sharedPublishedCount / $publishedInvitations) * 100, 1)
            : 0.0;

        // KPI-03b: Average RSVPs per RSVP-Enabled Published Invitation (State-Transition Accurate)
        $rsvpEnabledPublished = Invitation::where('status', 'published')
            ->where('is_rsvp_enabled', true)
            ->count();
        $rsvpAdoptionRate = $publishedInvitations > 0
            ? round(($rsvpEnabledPublished / $publishedInvitations) * 100, 1)
            : 0.0;
        $rsvpsOnEnabledPublished = RsvpResponse::whereHas(
            'invitation',
            fn ($q) => $q->where('status', 'published')->where('is_rsvp_enabled', true)
        )->count();
        $avgRsvpsPerEnabledPublished = $rsvpEnabledPublished > 0
            ? round($rsvpsOnEnabledPublished / $rsvpEnabledPublished, 1)
            : 0.0;
        $enabledWithRsvpCount = Invitation::where('status', 'published')
            ->where('is_rsvp_enabled', true)
            ->has('rsvps')
            ->count();
        $rsvpEnabledPublished30d = Invitation::where('status', 'published')
            ->where('is_rsvp_enabled', true)
            ->where('created_at', '>=', $since30Days)
            ->count();
        $rsvpsOnEnabledPublished30d = RsvpResponse::where('created_at', '>=', $since30Days)
            ->whereHas(
                'invitation',
                fn ($q) => $q->where('status', 'published')->where('is_rsvp_enabled', true)
            )
            ->count();
        $avgRsvpsPerEnabledPublished30d = $rsvpEnabledPublished30d > 0
            ? round($rsvpsOnEnabledPublished30d / $rsvpEnabledPublished30d, 1)
            : 0.0;

        // KPI-05: 3-Stage Privacy-Safe Invitation Footer Referral Attribution
        $inviteFooterVisits = FunnelDailyMetric::sumForKey(FunnelDailyMetric::KEY_INVITE_FOOTER_VISIT);
        $inviteFooterVisits30d = FunnelDailyMetric::sumForKey(FunnelDailyMetric::KEY_INVITE_FOOTER_VISIT, $since30Days);
        $referredSignups = User::where('role', 'user')
            ->where('signup_source', 'invite_footer')
            ->count();
        $referredSignups30d = User::where('role', 'user')
            ->where('signup_source', 'invite_footer')
            ->where('created_at', '>=', $since30Days)
            ->count();
        $referredSignupShare = $totalCouples > 0
            ? round(($referredSignups / $totalCouples) * 100, 1)
            : 0.0;
        $referredPublishedInvitations = Invitation::where('status', 'published')
            ->whereHas('user', fn ($q) => $q->where('role', 'user')->where('signup_source', 'invite_footer'))
            ->count();
        $referredPublishedInvitations30d = Invitation::where('status', 'published')
            ->where('created_at', '>=', $since30Days)
            ->whereHas('user', fn ($q) => $q->where('role', 'user')->where('signup_source', 'invite_footer'))
            ->count();
        $referredActivatedCouples = User::where('role', 'user')
            ->where('signup_source', 'invite_footer')
            ->whereHas('invitations', fn ($q) => $q->where('status', 'published'))
            ->count();
        $referredPublishedShare = $publishedInvitations > 0
            ? round(($referredPublishedInvitations / $publishedInvitations) * 100, 1)
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
                'published_rsvps' => $publishedRsvps,
                'confirmed_heads' => $confirmedHeads,
                'avg_rsvps_per_published' => $avgRsvpsPerPublished,
            ],
            'kpi01' => [
                'creation_starts' => $creationStarts,
                'published_invitations' => $publishedInvitations,
                'creation_to_published_rate' => $creationStarts > 0 ? min(100.0, round(($publishedInvitations / $creationStarts) * 100, 1)) : 0.0,
                'creation_starts_30d' => $creationStarts30d,
                'published_30d' => $published30d,
                'creation_to_published_rate_30d' => $creationStarts30d > 0 ? min(100.0, round(($published30d / $creationStarts30d) * 100, 1)) : 0.0,
            ],
            'kpi02' => [
                'shared_published_count' => $sharedPublishedCount,
                'shared_published_rate' => $sharedPublishedRate,
            ],
            'kpi03b' => [
                'rsvp_enabled_published' => $rsvpEnabledPublished,
                'rsvp_adoption_rate' => $rsvpAdoptionRate,
                'rsvps_on_enabled_published' => $rsvpsOnEnabledPublished,
                'avg_rsvps_per_enabled_published' => $avgRsvpsPerEnabledPublished,
                'enabled_with_rsvp_count' => $enabledWithRsvpCount,
                'rsvp_enabled_published_30d' => $rsvpEnabledPublished30d,
                'rsvps_on_enabled_published_30d' => $rsvpsOnEnabledPublished30d,
                'avg_rsvps_per_enabled_published_30d' => $avgRsvpsPerEnabledPublished30d,
            ],
            'kpi05' => [
                'invite_footer_visits' => $inviteFooterVisits,
                'invite_footer_visits_30d' => $inviteFooterVisits30d,
                'referred_signups' => $referredSignups,
                'referred_signups_30d' => $referredSignups30d,
                'referred_signup_share' => $referredSignupShare,
                'referred_published_invitations' => $referredPublishedInvitations,
                'referred_published_invitations_30d' => $referredPublishedInvitations30d,
                'referred_activated_couples' => $referredActivatedCouples,
                'referred_published_share' => $referredPublishedShare,
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
