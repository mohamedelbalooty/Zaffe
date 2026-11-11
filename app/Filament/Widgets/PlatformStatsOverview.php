<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $usersCount = User::count();
        $invitationsCount = Invitation::count();
        $publishedCount = Invitation::where('status', 'published')->count();
        $confirmedGuests = RsvpResponse::where('attendance_status', 'attending')->sum('guest_count');
        $templatesCount = InvitationTemplate::where('is_active', true)->count();

        return [
            Stat::make('المستخدمين المسجلين', $usersCount)
                ->description('إجمالي حسابات العرسان والمدراء')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('دعوات الزفاف', $invitationsCount)
                ->description("{$publishedCount} دعوة منشورة أونلاين")
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),

            Stat::make('إجمالي الضيوف المؤكدين', $confirmedGuests)
                ->description('تأكيدات الحضور عبر RSVP')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),

            Stat::make('قوالب وتصاميم الزفاف', $templatesCount)
                ->description('قوالب ملكية متاحة للتخصيص')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),
        ];
    }
}
