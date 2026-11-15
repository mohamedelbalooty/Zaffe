<?php

namespace App\Filament\Resources\RsvpResponses\Pages;

use App\Filament\Resources\RsvpResponses\RsvpResponseResource;
use App\Models\RsvpResponse;
use App\Services\PhoneService;
use App\Support\CsvSanitizer;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListRsvpResponses extends ListRecords
{
    protected static string $resource = RsvpResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('admin.actions.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (): StreamedResponse => $this->streamCsvExport()),
        ];
    }

    protected function streamCsvExport(): StreamedResponse
    {
        $filename = 'admin-rsvps-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                __('admin.csv.id'),
                __('admin.csv.invitation'),
                __('admin.csv.slug'),
                __('admin.csv.guest_name'),
                __('admin.csv.phone'),
                __('admin.csv.email'),
                __('admin.csv.status'),
                __('admin.csv.guest_count'),
                __('admin.csv.dietary_notes'),
                __('admin.csv.message'),
                __('admin.csv.submitted_at'),
            ]);

            RsvpResponse::with('invitation')
                ->latest()
                ->chunk(200, function ($rows) use ($handle): void {
                    foreach ($rows as $rsvp) {
                        $statusLabel = match ($rsvp->attendance_status) {
                            'attending', 'declined', 'tentative' => __('admin.options.attendance.'.$rsvp->attendance_status),
                            default => $rsvp->attendance_status,
                        };

                        fputcsv($handle, [
                            $rsvp->id,
                            CsvSanitizer::cell($rsvp->invitation?->title ?? '—'),
                            CsvSanitizer::cell($rsvp->invitation?->slug ?? '—'),
                            CsvSanitizer::cell($rsvp->guest_name),
                            CsvSanitizer::cell($rsvp->phone ? PhoneService::formatForDisplay($rsvp->phone) : '—'),
                            CsvSanitizer::cell($rsvp->email ?? '—'),
                            CsvSanitizer::cell((string) $statusLabel),
                            $rsvp->guest_count,
                            CsvSanitizer::cell($rsvp->dietary_notes ?? '—'),
                            CsvSanitizer::cell($rsvp->message ?? '—'),
                            optional($rsvp->created_at)->format('Y-m-d H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
