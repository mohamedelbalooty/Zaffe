<x-filament-panels::page>
    <div class="zaffe-mon-stack">
        <!-- Release 1 Funnel Metrics -->
        <div class="zaffe-mon-kpis">
            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.activation_rate') }}</p>
                <p class="zaffe-mon-value">{{ $funnel['activation_rate'] }}%</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.activation_sub', ['activated' => $funnel['activated_couples'], 'total' => $funnel['total_couples']]) }}</p>
            </div>

            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.invitation_statuses') }}</p>
                <p class="zaffe-mon-value zaffe-mon-value--emerald">{{ __('admin.monitor.published_inline', ['count' => $funnel['published_invitations']]) }}</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.statuses_sub', ['draft' => $funnel['draft_invitations'], 'archived' => $funnel['archived_invitations']]) }}</p>
            </div>

            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.avg_rsvps') }}</p>
                <p class="zaffe-mon-value zaffe-mon-value--gold">{{ $funnel['avg_rsvps_per_published'] }}</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.rsvps_sub', ['rsvps' => $funnel['total_rsvps'], 'heads' => $funnel['confirmed_heads']]) }}</p>
            </div>

            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.queue_notifications') }}</p>
                <p class="zaffe-mon-value">{{ __('admin.monitor.pending_inline', ['count' => $delivery['pending_jobs']]) }}</p>
                <p class="zaffe-mon-sub {{ $delivery['failed_jobs'] > 0 ? 'zaffe-mon-sub--danger' : '' }}">
                    {{ __('admin.monitor.queue_sub', ['failed' => $delivery['failed_jobs'], 'notifications' => $delivery['stored_notifications']]) }}
                </p>
            </div>
        </div>

        <!-- Release 1 KPI Instrumentation (KPI-01, KPI-02, KPI-03b, KPI-05a/b/c) -->
        <div class="zaffe-mon-kpis">
            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.kpi01_label') }}</p>
                <p class="zaffe-mon-value">{{ $kpi01['creation_to_published_rate'] }}%</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.kpi01_sub', ['published' => $kpi01['published_invitations'], 'starts' => $kpi01['creation_starts'], 'rate30' => $kpi01['creation_to_published_rate_30d']]) }}</p>
            </div>

            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.kpi02_label') }}</p>
                <p class="zaffe-mon-value zaffe-mon-value--emerald">{{ $kpi02['shared_published_rate'] }}%</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.kpi02_sub', ['shared' => $kpi02['shared_published_count'], 'published' => $funnel['published_invitations']]) }}</p>
            </div>

            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.kpi03b_label') }}</p>
                <p class="zaffe-mon-value zaffe-mon-value--gold">{{ $kpi03b['avg_rsvps_per_enabled_published'] }}</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.kpi03b_sub', ['rsvps' => $kpi03b['rsvps_on_enabled_published'], 'enabled' => $kpi03b['rsvp_enabled_published'], 'adoption' => $kpi03b['rsvp_adoption_rate'], 'with_rsvp' => $kpi03b['enabled_with_rsvp_count'], 'avg30' => $kpi03b['avg_rsvps_per_enabled_published_30d']]) }}</p>
            </div>

            <div class="zaffe-mon-card">
                <p class="zaffe-mon-label">{{ __('admin.monitor.kpi05_label') }}</p>
                <p class="zaffe-mon-value">{{ $kpi05['referred_signups'] }} / {{ $kpi05['invite_footer_visits'] }}</p>
                <p class="zaffe-mon-sub">{{ __('admin.monitor.kpi05_sub', ['visits30' => $kpi05['invite_footer_visits_30d'], 'signups_share' => $kpi05['referred_signup_share'], 'published' => $kpi05['referred_published_invitations'], 'couples' => $kpi05['referred_activated_couples'], 'pub_share' => $kpi05['referred_published_share']]) }}</p>
            </div>
        </div>

        <!-- Mail & Queue Configuration Summary (Secret-Free) -->
        <div class="zaffe-mon-card">
            <h3 class="zaffe-mon-section-title">{{ __('admin.monitor.mail_queue_section') }}</h3>
            <div class="zaffe-mon-configs">
                <div class="zaffe-mon-config-tile">
                    <span class="zaffe-mon-label" style="display:block;">{{ __('admin.monitor.mail_mailer') }}</span>
                    <span dir="ltr" class="zaffe-mon-mono">{{ $delivery['mail_mailer'] }}</span>
                </div>
                <div class="zaffe-mon-config-tile">
                    <span class="zaffe-mon-label" style="display:block;">{{ __('admin.monitor.queue_connection') }}</span>
                    <span dir="ltr" class="zaffe-mon-mono">{{ $delivery['queue_connection'] }}</span>
                </div>
                <div class="zaffe-mon-config-tile">
                    <span class="zaffe-mon-label" style="display:block;">{{ __('admin.monitor.resend_key') }}</span>
                    <span class="{{ $delivery['resend_configured'] ? 'zaffe-mon-badge-ok' : 'zaffe-mon-badge-warn' }}">
                        {{ $delivery['resend_configured'] ? __('admin.monitor.configured') : __('admin.monitor.not_configured') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Recent Failed Jobs (Sanitized) -->
        <div class="zaffe-mon-card">
            <h3 class="zaffe-mon-section-title">{{ __('admin.monitor.failed_jobs_section') }}</h3>

            @if(empty($recentFailedJobs))
                <div class="zaffe-mon-empty">
                    {{ __('admin.monitor.no_failed_jobs') }}
                </div>
            @else
                <div class="zaffe-mon-table-wrap">
                    <table class="zaffe-mon-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.monitor.col_id') }}</th>
                                <th>{{ __('admin.monitor.col_queue') }}</th>
                                <th>{{ __('admin.monitor.col_failed_at') }}</th>
                                <th>{{ __('admin.monitor.col_error_summary') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentFailedJobs as $job)
                                <tr>
                                    <td class="zaffe-mon-mono" style="margin-top:0;">{{ $job['id'] }}</td>
                                    <td dir="ltr">{{ $job['connection'] }} / {{ $job['queue'] }}</td>
                                    <td dir="ltr" class="zaffe-mon-mono" style="margin-top:0; font-size:0.75rem; white-space:nowrap;">{{ $job['failed_at'] }}</td>
                                    <td dir="ltr" class="zaffe-mon-error">{{ $job['error_summary'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
