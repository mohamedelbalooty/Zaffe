<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlatformStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $usersCount = User::count();
        $invitationsCount = Invitation::count();
        $publishedCount = Invitation::where('status', 'published')->count();
        $confirmedGuests = (int) RsvpResponse::where('attendance_status', 'attending')->sum('guest_count');
        $templatesCount = InvitationTemplate::availableForSelection()->count();
        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $preInvitedCount = Schema::hasTable('invitation_guests') ? InvitationGuest::count() : 0;
        $checkedInCount = Schema::hasTable('invitation_guests') ? InvitationGuest::whereNotNull('checked_in_at')->count() : 0;

        return [
            Stat::make(__('admin.stats.users'), $usersCount)
                ->description(__('admin.stats.users_desc'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make(__('admin.stats.invitations'), $invitationsCount)
                ->description(__('admin.stats.invitations_desc', ['count' => $publishedCount]))
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),

            Stat::make(__('admin.stats.confirmed_guests'), $confirmedGuests)
                ->description(__('admin.stats.confirmed_guests_desc'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),

            Stat::make(__('admin.stats.checkin_passes'), $preInvitedCount)
                ->description(__('admin.stats.checkin_passes_desc', ['count' => $checkedInCount]))
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('primary'),

            Stat::make(__('admin.stats.templates'), $templatesCount)
                ->description(__('admin.stats.templates_desc'))
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),

            Stat::make(__('admin.stats.failed_jobs'), $failedJobs)
                ->description(__('admin.stats.failed_jobs_desc'))
                ->descriptionIcon('heroicon-m-signal')
                ->color($failedJobs > 0 ? 'danger' : 'success'),
        ];
    }
}
