<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Release 1 Funnel Metrics -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('admin.monitor.activation_rate') }}</p>
                <p class="mt-2 text-2xl font-bold">{{ $funnel['activation_rate'] }}%</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.monitor.activation_sub', ['activated' => $funnel['activated_couples'], 'total' => $funnel['total_couples']]) }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('admin.monitor.invitation_statuses') }}</p>
                <p class="mt-2 text-2xl font-bold">{{ __('admin.monitor.published_inline', ['count' => $funnel['published_invitations']]) }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.monitor.statuses_sub', ['draft' => $funnel['draft_invitations'], 'archived' => $funnel['archived_invitations']]) }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('admin.monitor.avg_rsvps') }}</p>
                <p class="mt-2 text-2xl font-bold">{{ $funnel['avg_rsvps_per_published'] }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.monitor.rsvps_sub', ['rsvps' => $funnel['total_rsvps'], 'heads' => $funnel['confirmed_heads']]) }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('admin.monitor.queue_notifications') }}</p>
                <p class="mt-2 text-2xl font-bold">{{ __('admin.monitor.pending_inline', ['count' => $delivery['pending_jobs']]) }}</p>
                <p class="mt-1 text-xs {{ $delivery['failed_jobs'] > 0 ? 'text-danger-600 font-semibold' : 'text-gray-500' }}">
                    {{ __('admin.monitor.queue_sub', ['failed' => $delivery['failed_jobs'], 'notifications' => $delivery['stored_notifications']]) }}
                </p>
            </div>
        </div>

        <!-- Mail & Queue Configuration Summary (Secret-Free) -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-bold mb-4">{{ __('admin.monitor.mail_queue_section') }}</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 text-sm">
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <span class="block text-xs text-gray-500">{{ __('admin.monitor.mail_mailer') }}</span>
                    <span class="mt-1 block font-mono font-semibold">{{ $delivery['mail_mailer'] }}</span>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <span class="block text-xs text-gray-500">{{ __('admin.monitor.queue_connection') }}</span>
                    <span class="mt-1 block font-mono font-semibold">{{ $delivery['queue_connection'] }}</span>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <span class="block text-xs text-gray-500">{{ __('admin.monitor.resend_key') }}</span>
                    <span class="mt-1 block font-semibold {{ $delivery['resend_configured'] ? 'text-success-600' : 'text-warning-600' }}">
                        {{ $delivery['resend_configured'] ? __('admin.monitor.configured') : __('admin.monitor.not_configured') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Recent Failed Jobs (Sanitized) -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-bold mb-4">{{ __('admin.monitor.failed_jobs_section') }}</h3>

            @if(empty($recentFailedJobs))
                <p class="text-sm text-gray-500">{{ __('admin.monitor.no_failed_jobs') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-start">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-xs text-gray-500">
                                <th class="py-2 px-3 text-start">{{ __('admin.monitor.col_id') }}</th>
                                <th class="py-2 px-3 text-start">{{ __('admin.monitor.col_queue') }}</th>
                                <th class="py-2 px-3 text-start">{{ __('admin.monitor.col_failed_at') }}</th>
                                <th class="py-2 px-3 text-start">{{ __('admin.monitor.col_error_summary') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($recentFailedJobs as $job)
                                <tr>
                                    <td class="py-2 px-3 font-mono">{{ $job['id'] }}</td>
                                    <td class="py-2 px-3">{{ $job['connection'] }} / {{ $job['queue'] }}</td>
                                    <td class="py-2 px-3 font-mono text-xs">{{ $job['failed_at'] }}</td>
                                    <td class="py-2 px-3 font-mono text-xs text-danger-600">{{ $job['error_summary'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

